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
use Pinpoint\Common\PluginParser;

class GenProxyClassTemplateHelper extends AbstractClassFile
{
    private $factory;

    private $mClassNode = null; // class { }
    private $mTraitNode = null;

    private $extendTraitName;

    private $useBlockAr = [];

    private $trailUseAsArray = [];

    private $handleLeaveMethodCb = [];
    private $handleEndTraverseCb = [];
    public $methodJoinPoints = [];

    public function __construct(AspectClassHandle $classHandler, $prefix)
    {
        parent::__construct($prefix);
        $this->factory = new BuilderFactory();
        $this->methodJoinPoints = $classHandler->methodJoinPoints;
    }


    public function handleEnterNamespaceNode($node)
    {
        parent::handleEnterNamespaceNode($node);
    }

    public function handleEnterClassNode($node)
    {
        assert($node instanceof Node\Stmt\Class_);
        parent::handleEnterClassNode($node);

        $extendClass = $this->newNamePrefix . $node->name->toString();
        $this->mClassNode  = $this->factory->class(trim($node->name->toString()))->extend($extendClass);
        if (!empty($this->namespace)) {
            // if the proxy_class has namespace, add into use
            // if not, just ignore it . support for yii1
            $fullName = $this->namespace . '\\' . $extendClass;
            $this->useBlockAr[] = array($fullName, null);
        }

        switch ($node->flags) {
            case Node\Stmt\Class_::MODIFIER_FINAL:
                $this->mClassNode->makeFinal();
                break;
            case Node\Stmt\Class_::MODIFIER_ABSTRACT:
                $this->mClassNode->makeAbstract();
                break;
            default:
                break;
        }

        $this->handleLeaveMethodCb = array($this, 'handleClassLeaveMethodNode');
        $this->handleEndTraverseCb = array($this, 'handleAfterTraverseClass');
    }

    public function handleEnterTraitNode(&$node)
    {
        assert($node instanceof Node\Stmt\Trait_);
        parent::handleEnterTraitNode($node);
        $this->mTraitNode  = $this->factory->trait(trim($node->name->toString()));
        $this->extendTraitName = $this->newNamePrefix . $node->name->toString();
        $this->handleLeaveMethodCb = array($this, 'handleTraitLeaveMethodNode');
        $this->handleEndTraverseCb = array($this, 'handleAfterTraverseTrait');
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
        Logger::Inst()->debug("generate pinpoint code block for '$originMethodName'");
        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Method());

        $thisMethod = $this->factory->method($originMethodName);

        // 1.1 public/protect/private/
        if ($node->flags & Node\Stmt\Class_::MODIFIER_PUBLIC) {
            $thisMethod->makePublic();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PRIVATE) {
            Logger::Inst()->debug("'$originMethodName' is a private, changes to protected");
            $thisMethod->makeProtected();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_ABSTRACT) {
            $thisMethod->makeAbstract();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_FINAL) {
            $thisMethod->makeFinal();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PROTECTED) {
            $thisMethod->makeProtected();
        }

        //1.2 gen $this->
        if ($node->flags & Node\Stmt\Class_::MODIFIER_STATIC) {
            $thisMethod->makeStatic();
            $selfVar = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('null')));
            Logger::Inst()->debug("'$originMethodName' is a static function");
        } else {
            $selfVar = new Node\Arg(new Node\Expr\Variable('this'));
        }

        $methodParams  = array_merge([$funcVar, $selfVar], GenProxyClassTemplateHelper::convertParamsName2Arg($node->params));

        $thisMethod->addParams($node->params);
        if ($node->returnType) {
            $thisMethod->setReturnType($node->returnType);
        }

        $varName = '_pinpoint_' . $originMethodName . '_var';
        $retName = '_pinpoint_' . $originMethodName . '_ret';

        /// $_pinpoint_method_var = new pinpoint\Plugins\CommonPlugins(__FUNCTION__,self,$p);
        $newPluginsStm = new Node\Stmt\Expression(new Node\Expr\Assign(
            new Node\Expr\Variable($varName),
            $this->factory->new(new Node\Name\FullyQualified($monitorClassFullName), $methodParams)
        ));

        $thisMethod->addStmt($newPluginsStm);
        // $var = null;
        $newVar = new Node\Stmt\Expression(new Node\Expr\Assign(
            new Node\Expr\Variable($retName),
            new Node\Expr\ConstFetch(new Node\Name('null'))
        ));
        $thisMethod->addStmt($newVar);

        $tryBlock = [];
        $catchNode = [];

        // $plugin->onBefore();
        $tryBlock[] = new Node\Stmt\Expression(
            $this->factory->methodCall(new Node\Expr\Variable($varName), "onBefore")
        );

        if ($this->hasRet) {
            Logger::Inst()->debug("'$originMethodName' has return value ");
            /// $ret = paraent::$originMethodName();
            $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
                new Node\Expr\Variable($retName),
                new Node\Expr\StaticCall(
                    new Node\Name("parent"),
                    new Node\Identifier($originMethodName),
                    GenProxyClassTemplateHelper::convertParamsName2Arg($node->params)
                )
            ));

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

            $tryBlock[] = new Node\Stmt\Expression($this->factory->staticCall(
                new Node\Name("parent"),
                new Node\Identifier($originMethodName),
                GenProxyClassTemplateHelper::convertParamsName2Arg($node->params)
            ));

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

        $thisMethod->addStmt($tryCatchFinallyNode);

        $this->mClassNode->addStmt($thisMethod);
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

    public function handleTraitLeaveMethodNode($node, $info)
    {
        /// todo this methodName looks ugly

        /// - check use , add  use Proxied_Foo { }
        /// - insert alias use Proxied_Foo::xxx as Foo_xxxx
        /// - new function xxxx

        assert($node instanceof Node\Stmt\ClassMethod);

        list($mode, $namespace, $className) = $info;

        // foo_1
        $originMethodName = $node->name->toString();

        $np = empty($namespace) ? $className  : $namespace . '\\' . $className;
        $np_ar = [$np, null];
        // use CommonPlugins\Plugins;
        if (!static::itemInArray($this->useBlockAr, $np_ar)) {
            $this->useBlockAr[] = $np_ar;
        }

        // $this->extendTraitName::$originMethodName as $this->extendTraitName_$originMethodName;
        $this->trailUseAsArray[] = $originMethodName;
        $extendMethodName = $this->extendTraitName . '_' . $originMethodName;


        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Method());

        // public function funcName(){}
        $thisMethod = $this->factory->method($originMethodName);

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PUBLIC) {
            $thisMethod->makePublic();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PRIVATE) {
            $thisMethod->makePrivate();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_ABSTRACT) {

            $thisMethod->makeAbstract();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_FINAL) {
            $thisMethod->makeFinal();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PROTECTED) {
            $thisMethod->makeProtected();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_STATIC) {
            $thisMethod->makeStatic();
            $selfVar = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('null')));
        } else {
            $selfVar = new Node\Arg(new Node\Expr\Variable('this'));
        }

        $methodParams  = array_merge([$funcVar, $selfVar], GenProxyClassTemplateHelper::convertParamsName2Arg($node->params));

        $thisMethod->addParams($node->params);
        if ($node->returnType) {
            $thisMethod->setReturnType($node->returnType);
        }

        $varName = $className . '_' . $originMethodName . '_var';
        $retName = $className . '_' . $originMethodName . '_ret';

        /// $var = new CommonPlugins(__FUNCTION__,self,$p);
        $newPluginsStm = new Node\Stmt\Expression(new Node\Expr\Assign(
            new Node\Expr\Variable($varName),
            $this->factory->new($className, $methodParams)
        ));

        $thisMethod->addStmt($newPluginsStm);
        // $var = null;
        $newVar = new Node\Stmt\Expression(new Node\Expr\Assign(
            new Node\Expr\Variable($retName),
            new Node\Expr\ConstFetch(new Node\Name('null'))
        ));
        $thisMethod->addStmt($newVar);

        $tryBlock = [];
        $catchNode = [];

        // $plugin->onBefore();
        $tryBlock[] = new Node\Stmt\Expression(
            $this->factory->methodCall(new Node\Expr\Variable($varName), "onBefore")
        );

        if ($this->hasRet) {
            /// $ret = $this->method(&...$args);
            $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\Assign(
                new Node\Expr\Variable($retName),
                new Node\Expr\MethodCall(
                    new Node\Expr\Variable("this"),
                    new Node\Identifier($extendMethodName),
                    GenProxyClassTemplateHelper::convertParamsName2Arg($node->params)
                )
            ));

            /// $var->onEnd($ret);
            if ($mode & PluginParser::END) {
                $tryBlock[] = new Node\Stmt\Expression(
                    $this->factory->methodCall(
                        new Node\Expr\Variable($varName),
                        "onEnd",
                        [new Node\Expr\Variable($retName)]
                    )
                );
            }

            /// return $var;
            $tryBlock[] = new Node\Stmt\Return_(new Node\Expr\Variable($retName));
        } else {
            /// $this->>$originMethodName();
            $tryBlock[] = new Node\Stmt\Expression(new Node\Expr\MethodCall(
                new Node\Expr\Variable("this"),
                new Node\Identifier($extendMethodName),
                GenProxyClassTemplateHelper::convertParamsName2Arg($node->params)
            ));

            /// $var->onEnd($ret);
            if ($mode & PluginParser::END) {
                $tryBlock[] = new Node\Stmt\Expression(
                    $this->factory->methodCall(
                        new Node\Expr\Variable($varName),
                        "onEnd",
                        [new Node\Expr\Variable($retName)]
                    )
                );
            }
        }

        $expArgs = [];
        $expArgs[] = new Node\Arg(new Node\Expr\Variable('e'));

        if ($mode & PluginParser::EXCEPTION) {

            $catchBlock[] = new Node\Stmt\Expression(
                $this->factory->methodCall(
                    new Node\Expr\Variable($varName),
                    "onException",
                    $expArgs
                )
            );
        }

        $catchBlock[] = new Node\Stmt\Throw_(new Node\Expr\Variable("e"));

        $catchNode[] = new Node\Stmt\Catch_(
            [new Node\Name('\Exception')],
            new Node\Expr\Variable('e'),
            $catchBlock
        );

        $tryCatchFinallyNode = new Node\Stmt\TryCatch($tryBlock, $catchNode);

        $thisMethod->addStmt($tryCatchFinallyNode);

        $this->mTraitNode->addStmt($thisMethod);
    }

    public function handleLeaveMethodNode($node)
    {
        $methodName = trim($node->name->toString());
        if (array_key_exists($methodName, $this->methodJoinPoints)) {
            call_user_func_array(
                $this->handleLeaveMethodCb,
                [
                    $node,
                    $this->methodJoinPoints[$methodName]
                ]
            );
        }
    }

    public function handleAfterTraverseClass()
    {
        $useNodes = [];
        $this->fileName = $this->className;

        $this->useBlockArToNodes($useNodes);
        if (!empty($this->namespace)) {
            $this->fileNode = $this->factory->namespace($this->namespace);
            if (count($useNodes) > 0) {
                $this->fileNode->addStmts($useNodes);
            }
            $this->fileNode->addStmt($this->mClassNode);
            return array($this->fileNode->getNode());
        } else {
            $this->fileNode = []; //$this->factory->namespace($this->namespace);
            foreach ($useNodes as $node) {
                $this->fileNode[] = $node->getNode();
            }
            $this->fileNode[] = $this->mClassNode->getNode();
            return $this->fileNode;
        }
    }

    private function useBlockArToNodes(&$stmNode)
    {

        foreach ($this->useBlockAr as $var) {

            if (isset($var[1])) { // the second must be alias : use class as foo
                $node = $this->factory->use($var[0])->as($var[1]);
            } else { //== 1
                $node = $this->factory->use($var[0]);
            }

            $stmNode[] = $node;
        }
    }

    public function handleAfterTraverseTrait()
    {
        $useNodes = [];
        $this->useBlockArToNodes($useNodes);

        // use Proxied_Foo{}
        $useTraitNode = $this->factory->useTrait($this->extendTraitName);

        foreach ($this->trailUseAsArray as $alias) {
            // $extendMethodName::thisfuncName as $this->extendTraitName.'_'.$originMethodName;
            $useTraitNode->with($this->factory->traitUseAdaptation($this->extendTraitName, $alias)->as($this->extendTraitName . '_' . $alias));
        }

        $this->mTraitNode->addStmt($useTraitNode);
        // todo does need to handle trait without any namespace
        $this->fileNode = $this->factory->namespace($this->namespace)
            ->addStmts($useNodes)
            ->addStmt($this->mTraitNode);

        $this->fileName = $this->traitName;

        return array($this->fileNode->getNode());
    }

    public function handleAfterTraverse($nodes)
    {
        $this->newAstNode = call_user_func_array($this->handleEndTraverseCb, []);
    }

    function handleLeaveNamespace($nodes)
    {
        // do nothing
    }

    function handleEnterClassConstFetch($node)
    {
    }
    function handleEnterNew($node)
    {
    }
    function handleEnterFuncCall($node)
    {
    }

    function handlerUseNode($node)
    {
        assert($node instanceof Node\Stmt\Use_);
        foreach ($node->uses as $uses) {
            $this->useBlockAr[] = array($uses->name->toString(), $uses->alias ?  $uses->alias->name : null);
        }
    }


    function handleMagicConstNode($node)
    {
    }

    function handleLeaveClassNode($node)
    {
    }
}
