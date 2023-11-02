<?php

declare(strict_types=1);
/**
 * Copyright 2020-present NAVER Corp.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * User: eeliu
 * Date: 2/13/19
 * Time: 4:41 PM
 */

namespace Pinpoint\Common;

use Pinpoint\Common\AbstractClassFile;
use PhpParser\BuilderFactory;
use PhpParser\Node;

const _originNamePrefix_ = "__pinpoint__";

class GenProxyClassTemplateHelper extends AbstractClassFile
{
    private BuilderFactory $factory;
    private $useBlockAr = [];
    private $handleLeaveMethodCb = [];
    public $methodJoinPoints = [];
    private $newMethodsStmts = [];

    protected $originClassFileDir;
    protected $originClassFilePath;
    protected $suffix_use = [];
    protected $classAliasSet = [];
    protected $funcAlias = [];
    protected $methodJoint;

    public function __construct(AspectClassHandle $classHandler)
    {
        parent::__construct();
        $this->factory = new BuilderFactory();
        $this->methodJoinPoints = $classHandler->methodJoinPoints;
        $this->classAliasSet  = $classHandler->classAlias;
        $this->funcAlias = $classHandler->funcAlias;
        $this->methodJoint = $classHandler->methodJoinPoints;
    }


    public function handleEnterNamespaceNode($node)
    {
        parent::handleEnterNamespaceNode($node);
    }

    private function getRealNp($node)
    {

        if ($node instanceof Node\Name\FullyQualified)         // Use directly access
        {
            return $node->toString();
        } elseif ($node instanceof Node\Expr\Variable) { //#16 support new a variable
            return $node->name;
        } elseif ($node instanceof Node\Name) {    // Use namespace suggestion
            $prefixNm = $node->getFirst();
            if (isset($this->suffix_use[$prefixNm])) {
                $namePrefix = $this->suffix_use[$prefixNm];
                $nm =  $namePrefix . "\\" . $node->toString();
                return $nm;
            } else {
                return $node->toString();
            }
        }
    }

    private function renderClassName($node, $filer)
    {
        $classFullName = $this->getRealNp($node);

        if (isset($filer[$classFullName])) {
            $methodNewName = $filer[$classFullName];
            return new Node\Name\FullyQualified($methodNewName);
        }
        return $node;
    }

    public function handleEnterNew($node)
    {
        assert($node instanceof Node\Expr\New_);
        $node->class =  $this->renderClassName($node->class, $this->classAliasSet);
        return $node;
    }

    public function handleEnterClassConstFetch($node)
    {
        assert($node instanceof Node\Expr\ClassConstFetch);
        $node->class =  $this->renderClassName($node->class, $this->classAliasSet);
        return $node;
    }

    public function handleEnterFuncCall($node)
    {
        assert($node instanceof Node\Expr\FuncCall);
        if ($node->name instanceof Node\Expr\Variable) {
            // not support anonymous function
        } else {
            $node->name =  $this->renderFunName($node->name, $this->funcAlias);
        }
        return $node;
    }


    public function handleEnterClassNode($node)
    {
        assert($node instanceof Node\Stmt\Class_);
        parent::handleEnterClassNode($node);

        $this->handleLeaveMethodCb = array($this, 'handleClassLeaveMethodNode');
    }

    public function handleEnterTraitNode(&$node)
    {
        assert($node instanceof Node\Stmt\Trait_);
        // parent::handleEnterTraitNode($node);
        // $this->mTraitNode  = $this->factory->trait(trim($node->name->toString()));
        // $this->extendTraitName = $this->newNamePrefix . $node->name->toString();
        // $this->handleLeaveMethodCb = array($this, 'handleTraitLeaveMethodNode');
        // $this->handleEndTraverseCb = array($this, 'handleAfterTraverseTrait');
    }

    public static function convertParamsName2Arg($params)
    {
        assert(is_array($params));

        $args = [];

        foreach ($params as $param) {
            assert($param instanceof Node\Param);
            $args[] = new Node\Arg($param->var);
        }

        return  $args;
    }


    public function handleClassLeaveMethodNode($node, $monitorClassFullName)
    {
        assert($node instanceof Node\Stmt\ClassMethod);

        // 1. make a new method overriding parent's
        $originMethodName = $node->name->toString();
        $newMethodName = $this->methodNewName($originMethodName);
        $node->name = new Node\Name\FullyQualified($newMethodName);

        Logger::Inst()->debug("generate pinpoint code block for '$originMethodName'");
        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Method());

        $jointMethod = $this->factory->method($originMethodName);

        $docComments = <<<EOD
        /*
        * $originMethodName auto-generated by pinpoint-apm/pinpoint-php-aop
        */
        EOD;
        $jointMethod->setDocComment($docComments);
        // 1.1 public/protect/private/
        if ($node->flags & Node\Stmt\Class_::MODIFIER_PUBLIC) {
            $jointMethod->makePublic();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PRIVATE) {
            Logger::Inst()->debug("'$originMethodName' is a private");
            $jointMethod->makePrivate();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_ABSTRACT) {
            $jointMethod->makeAbstract();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_FINAL) {
            $jointMethod->makeFinal();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PROTECTED) {
            $jointMethod->makeProtected();
        }

        //1.2 gen $this->
        $isStatic = false;
        if ($node->flags & Node\Stmt\Class_::MODIFIER_STATIC) {
            $isStatic = true;
            $jointMethod->makeStatic();
            $selfVar = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('null')));
            Logger::Inst()->debug("'$originMethodName' is a static function");
        } else {
            $selfVar = new Node\Arg(new Node\Expr\Variable('this'));
        }

        $methodParams  = array_merge([$funcVar, $selfVar], GenProxyClassTemplateHelper::convertParamsName2Arg($node->params));

        $jointMethod->addParams($node->params);
        if ($node->returnType) {
            $jointMethod->setReturnType($node->returnType);
        }

        $varName = '_pinpoint_' . $originMethodName . '_var';
        $retName = '_pinpoint_' . $originMethodName . '_ret';

        /// $_pinpoint_method_var = new pinpoint\Plugins\CommonPlugins(__FUNCTION__,self,$p);
        $newPluginsStm = new Node\Stmt\Expression(new Node\Expr\Assign(
            new Node\Expr\Variable($varName),
            $this->factory->new(new Node\Name\FullyQualified($monitorClassFullName), $methodParams)
        ));

        $jointMethod->addStmt($newPluginsStm);
        // $var = null;
        $newVar = new Node\Stmt\Expression(new Node\Expr\Assign(
            new Node\Expr\Variable($retName),
            new Node\Expr\ConstFetch(new Node\Name('null'))
        ));
        $jointMethod->addStmt($newVar);

        $tryBlock = [];
        $catchNode = [];

        // $plugin->onBefore();
        $tryBlock[] = new Node\Stmt\Expression(
            $this->factory->methodCall(new Node\Expr\Variable($varName), "onBefore")
        );

        if ($this->hasRet) {
            Logger::Inst()->debug("'$originMethodName' has return value ");

            if ($isStatic) {
                /// $ret = self::newMethodName();
                $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
                    new Node\Expr\Variable($retName),
                    new Node\Expr\StaticCall(
                        new Node\Name("self"),
                        new Node\Identifier($newMethodName),
                        GenProxyClassTemplateHelper::convertParamsName2Arg($node->params)
                    )
                ));
            } else {
                /// $ret = $this->$newMethodName();
                $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
                    new Node\Expr\Variable($retName),
                    new Node\Expr\MethodCall(
                        new Node\Expr\Variable("this"),
                        new Node\Identifier($newMethodName),
                        GenProxyClassTemplateHelper::convertParamsName2Arg($node->params)
                    )
                ));
            }

            /// $var->onEnd($ret);
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(
                    new Node\Expr\Variable($varName),
                    "onEnd",
                    [new Node\Expr\Variable($retName)]
                )
            );

            /// return $var;
            $tryBlock[] = new Node\Stmt\Return_(new Node\Expr\Variable($retName));
        } else {
            /// paraent::$originMethodName();

            if ($isStatic) {
                /// $ret = self::newMethodName();
                $tryBlock[] = new Node\Stmt\Expression(
                    new Node\Expr\StaticCall(
                        new Node\Name("self"),
                        new Node\Identifier($newMethodName),
                        GenProxyClassTemplateHelper::convertParamsName2Arg($node->params)
                    )
                );
            } else {
                /// $ret = $this->$newMethodName();
                $tryBlock[] = new Node\Stmt\Expression(
                    new Node\Expr\MethodCall(
                        new Node\Expr\Variable("this"),
                        new Node\Identifier($newMethodName),
                        GenProxyClassTemplateHelper::convertParamsName2Arg($node->params)
                    )
                );
            }

            /// $var->onEnd($ret);
            $tryBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(
                    new Node\Expr\Variable($varName),
                    "onEnd",
                    [new Node\Expr\Variable($retName)]
                )
            );
        }

        $expArgs = [];
        $expArgs[] = new Node\Arg(new Node\Expr\Variable('e'));


        $catchBlock[] = new Node\Stmt\Expression(
            $this->factory->methodCall(
                new Node\Expr\Variable($varName),
                "onException",
                $expArgs
            )
        );

        $catchBlock[] = new Node\Stmt\Throw_(new Node\Expr\Variable("e"));

        $catchNode[] = new Node\Stmt\Catch_(
            [new Node\Name('\Exception')],
            new Node\Expr\Variable('e'),
            $catchBlock
        );

        $tryCatchFinallyNode = new Node\Stmt\TryCatch($tryBlock, $catchNode);

        $jointMethod->addStmt($tryCatchFinallyNode);

        $this->newMethodsStmts[] = $jointMethod->getNode();
    }

    public static function itemInArray($ar, $v)
    {
        $new = array_filter($ar, function ($a) use ($v) {
            if ($a == $v) {
                return $a;
            }
        });

        return !empty($new);
    }

    private function methodNewName($olderName): string
    {
        return _originNamePrefix_ . $olderName;
    }

    public function handleLeaveMethodNode($node)
    {
        $methodName = trim($node->name->toString());
        if (array_key_exists($methodName, $this->methodJoinPoints)) {
            // rename method name
            call_user_func_array(
                $this->handleLeaveMethodCb,
                [
                    $node,
                    $this->methodJoinPoints[$methodName]
                ]
            );
        }
    }

    public function renderFunName(&$node, $filer)
    {
        $classFullName = $node->toString();
        if (isset($filer[$classFullName])) {
            $methodNewName = $filer[$classFullName];
            return new Node\Name\FullyQualified($methodNewName);
        }
        return $node;
    }


    /** rename the class Proxied_foo
     * @param $node
     */
    public function handleLeaveClassNode($node)
    {
        assert($node instanceof Node\Stmt\Class_);
        array_push($node->stmts, ...$this->newMethodsStmts);
    }

    public function handleAfterTraverse($nodes)
    {
        $this->newAstNode = $nodes;
    }

    public function handleLeaveNamespace($nodes)
    {
        return $nodes;
    }


    public function handleFullyQualifiedNode(&$node)
    {
        assert($node instanceof Node\Name\FullyQualified);
        $name = $node->toString();
        if (isset($this->classAliasSet[$name])) {
            return new Node\Name\FullyQualified($this->classAliasSet[$name]);
        } else {
            return $node;
        }
    }

    function handlerUseNode($node)
    {
        assert($node instanceof Node\Stmt\Use_);
        foreach ($node->uses as $uses) {
            $this->useBlockAr[] = array($uses->name->toString(), $uses->alias ?  $uses->alias->name : null);
        }

        //rename the nodes
        assert($node instanceof Node\Stmt\Use_);
        $type = $node->type;
        if ($type == Node\Stmt\Use_::TYPE_CONSTANT) {
            return;
        }

        // replace the exactly match
        // use A/B/C; -> Plugins/A/B/C
        foreach ($node->uses as &$uses) {
            $fullName = trim($uses->name->toString(), "\ \\");

            if ($type == Node\Stmt\Use_::TYPE_FUNCTION) {
                // use function Math\{add, subtract};
                if (array_key_exists($fullName, $this->funcAlias)) {
                    $methodNewName = new Node\Name($this->funcAlias[$fullName]);
                    $uses->name = $methodNewName;
                    Logger::Inst()->debug("found funcAlias:'$fullName' -> '$this->funcAlias[$fullName]' ");
                }
            } else {
                // use ABC\Math;
                if (array_key_exists($fullName, $this->classAliasSet)) {
                    $methodNewName = new Node\Name($this->classAliasSet[$fullName]);
                    $uses->name = $methodNewName;
                    Logger::Inst()->debug("found classAlias:'$fullName' -> '$this->classAliasSet[$fullName]' ");
                }
            }
        }
    }


    function handlerUseUseNode(&$node)
    {
        assert($node instanceof  Node\Stmt\UseUse);

        // parse use A\B\C as ABC;
        // here , A\B\C is hidden by ABC
        // so re-add ABC
        if ($node->alias) {
            $namespaceAlias = $node->alias->name;
            $hiddenClassAlias = [];
            $methodNewName = trim($node->name->toString(), "\ \\");
            foreach ($this->classAliasSet as $clName => $classAlias) {
                if (strpos($clName, $methodNewName) === 0) {
                    $classNewName = str_replace($methodNewName, $namespaceAlias, $clName);
                    $hiddenClassAlias[$classNewName] = $classAlias;
                }
            }
            if (!empty($hiddenClassAlias)) {
                Logger::Inst()->debug("found hiddenClassAlias:'$hiddenClassAlias'");
                $this->classAliasSet += $hiddenClassAlias;
            }
            return;
        }

        // parse use A/B/C, but the nm_ is A/B/C/D
        $suffixNm = $node->name->getLast();
        if (!$node->name->isUnqualified()) {
            $this->suffix_use[$suffixNm] = $node->name->slice(0, -1)->toString();
        }
    }
}
