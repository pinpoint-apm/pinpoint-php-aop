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
 * Date: 2/2/19
 * Time: 10:28 AM
 */

namespace Pinpoint\Common;

use PhpParser\Node;
use Pinpoint\Common\ClassFile;

class GenOriginClassTemplateHelper extends ClassFile
{
    protected $originClassFileDir;
    protected $originClassFilePath;
    protected $useBlockAr = [];
    protected $classAliasSet = [];
    protected $funcAlias = [];
    protected $suffix_use = [];
    protected $methodJoint;
    // private $joinClass;
    public function __construct(JoinClass $joinClass, string $namePrefix)
    {
        parent::__construct($namePrefix);
        $path = Utils::findFile($joinClass->name);

        $this->originClassFileDir = dirname($path);
        $this->originClassFilePath = $path;
        // $this->joinClass = $joinClass;
        $this->classAliasSet  = $joinClass->classAlias;
        $this->funcAlias = $joinClass->funcAlias;
        $this->methodJoint = $joinClass->methodJoinPoints;
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


    public function renderClassName($node, $filer)
    {
        $classFullName = $this->getRealNp($node);

        if (isset($filer[$classFullName])) {
            $newName = $filer[$classFullName];
            return new Node\Name\FullyQualified($newName);
        }
        return $node;
    }

    public function renderFunName(&$node, $filer)
    {
        $classFullName = $node->toString();
        if (isset($filer[$classFullName])) {
            $newName = $filer[$classFullName];
            return new Node\Name\FullyQualified($newName);
        }
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


    /** rename the class Proxied_foo
     * @param $node
     */
    public function handleLeaveClassNode($node)
    {
        assert($node instanceof Node\Stmt\Class_);
        $className = $this->newNamePrefix . $node->name->toString();

        $node->name = new Node\Identifier($className);

        $this->className = empty($this->namespace) ? ($className) : $this->namespace . '\\' . $className;
        $this->myLoaderName = $this->className;

        if ($node->flags & Node\Stmt\Class_::MODIFIER_FINAL) {
            /// remove FINAL flag
            $node->flags = $node->flags & (~(Node\Stmt\Class_::MODIFIER_FINAL));
        }
    }

    /**
     * rename trait Foo{} => trait Proxy_Foo{}
     * @param $node
     */
    public function handleLeaveTraitNode(&$node)
    {
        assert($node instanceof Node\Stmt\Trait_);
        $className = $this->newNamePrefix . $node->name->toString();

        $node->name = new Node\Identifier($className);

        $this->traitName = empty($this->namespace) ? ($className) : ($this->namespace . '\\' . $className);
        $this->myLoaderName = $this->traitName;
    }


    public function handleLeaveMethodNode($node)
    {
        $func = trim($node->name->toString());
        if (!array_key_exists($func, $this->methodJoint)) {
            return;
        }

        assert($node instanceof Node\Stmt\ClassMethod);
        if ($node->flags &  Node\Stmt\Class_::MODIFIER_PRIVATE) {
            // unset private
            $node->flags = $node->flags & (~Node\Stmt\Class_::MODIFIER_PRIVATE);

            // set protect
            $node->flags = $node->flags | (Node\Stmt\Class_::MODIFIER_PROTECTED);
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_FINAL) {
            $node->flags = $node->flags & (~Node\Stmt\Class_::MODIFIER_FINAL);
        }
    }

    /**
     * @obsoleted
     * convert \Exception to Exception
     * try to use namespace cover
     * @param $node
     * @return Node\Name
     */
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

    public function handleMagicConstNode($node)
    {
        switch ($node->getName()) {
            case '__FILE__':
                return new Node\Scalar\String_($this->originClassFilePath);
            case '__DIR__':
                return new Node\Scalar\String_($this->originClassFileDir);
            case '__FUNCTION__':
                return new Node\Scalar\String_($this->classMethod);
            case '__CLASS__':
                return new Node\Scalar\String_($this->className);
            case '__METHOD__':
                return new Node\Scalar\String_($this->classMethod);
            case '__NAMESPACE__':
                return new Node\Scalar\String_($this->namespace);
            case '__LINE__':
                return new Node\Scalar\LNumber($node->getAttribute('startLine'));
            default:
                break;
        }
        return $node;
    }


    public function handleLeaveNamespace($nodes)
    {
        return $nodes;
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
            $newName = trim($node->name->toString(), "\ \\");
            foreach ($this->classAliasSet as $clName => $classAlias) {
                if (strpos($clName, $newName) === 0) {
                    $classNewName = str_replace($newName, $namespaceAlias, $clName);
                    $hiddenClassAlias[$classNewName] = $classAlias;
                }
            }
            if (!empty($hiddenClassAlias)) {
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

    function handleAfterTraverse($nodes)
    {
        $this->newAstNode = $nodes;
    }

    function handlerUseNode($node)
    {
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
                    $newName = new Node\Name($this->funcAlias[$fullName]);
                    $uses->name = $newName;
                }
            } else {
                // use ABC\Math;
                if (array_key_exists($fullName, $this->classAliasSet)) {
                    $newName = new Node\Name($this->classAliasSet[$fullName]);
                    $uses->name = $newName;
                }
            }
        }
    }
}
