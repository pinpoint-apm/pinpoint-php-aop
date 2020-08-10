<?php
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

namespace pinpoint\Common;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use pinpoint\Common\PluginParser;
use pinpoint\Common\ClassFile;

class GenProxiedClassFileHelper extends ClassFile
{

    protected $orgDir;
    protected $orgFile;


    public function __construct($fullFile,$internalFuncs,$prefix = 'Proxied_')
    {
        parent::__construct($prefix);

        $this->orgDir = dirname($fullFile);
        $this->orgFile = $fullFile;
        $this->prefix = $prefix;
    }


    /** rename the class Proxied_foo
     * @param $node
     */
    public function handleLeaveClassNode(&$node)
    {
        assert($node instanceof Node\Stmt\Class_);
        $className =$this->prefix.$node->name->toString();

        $node->name = new Node\Identifier($className);

        $this->className = $this->namespace.'\\'.$className;
        $this->name = $this->className;

        if($node->flags & Node\Stmt\Class_::MODIFIER_FINAL)
        {
            /// remove FINAL flag
            $node->flags = $node->flags & ( ~(Node\Stmt\Class_::MODIFIER_FINAL) );
        }
    }

    /**
     * rename trait Foo{} => trait Proxed_Foo{}
     * @param $node
     */
    public function handleLeaveTraitNode(&$node)
    {
        assert($node instanceof Node\Stmt\Trait_);
        $className =$this->prefix.$node->name->toString();

        $node->name = new Node\Identifier($className);

        $this->traitName = $this->namespace.'\\'.$className;
        $this->name = $this->traitName;
    }


    public function handleLeaveMethodNode(&$node,&$info)
    {
        assert($node instanceof Node\Stmt\ClassMethod);
        if($node->flags &  Node\Stmt\Class_::MODIFIER_PRIVATE)
        {
            // unset private
            $node->flags = $node->flags &(~Node\Stmt\Class_::MODIFIER_PRIVATE);

            // set protect
            $node->flags = $node->flags | (Node\Stmt\Class_::MODIFIER_PROTECTED);
        }

        if($node->flags & Node\Stmt\Class_::MODIFIER_FINAL)
        {
            $node->flags = $node->flags &(~Node\Stmt\Class_::MODIFIER_FINAL);
        }

    }


    public function handleFullyQualifiedNode(&$node)
    {
        assert($node instanceof Node\Name\FullyQualified);

        $newNode = new Node\Name($node->toString());

        return $newNode;
    }

    public function addRequiredFile($fullName)
    {
        // modify the namespace
        if(!in_array($fullName,$this->appendingFile))
        {
            $this->appendingFile[] = $fullName;
        }
    }

    public function handleMagicConstNode(&$node)
    {
        switch ($node->getName())
        {
            case '__FILE__':
                return new Node\Scalar\String_($this->orgFile);
                break;
            case '__DIR__':
                return new Node\Scalar\String_($this->orgDir);
                break;
            case '__FUNCTION__':
                return new Node\Scalar\String_($this->classMethod);
                break;
            case '__CLASS__':
                return new Node\Scalar\String_($this->className);
                break;
            case '__METHOD__':
                return new Node\Scalar\String_($this->classMethod);
                break;
            case '__NAMESPACE__':
                return new Node\Scalar\String_($this->namespace);
                break;
            default:
                break;
        }

    }


    public function handleLeaveNamespace(&$nodes)
    {
        //todo
        // This is just a temporary solution,just some tricks on origNode.
        // Maybe one day php-parse could handle such scene
        assert($nodes instanceof Node\Stmt\Namespace_);
        foreach ($this->appendingFile as $fullPath)
        {
            $expression= new Node\Stmt\Expression(
                new Node\Expr\Include_(
                    new Node\Expr\BinaryOp\Concat(new Node\Expr\ConstFetch( new Node\Name("AOP_CACHE_DIR")),new Node\Scalar\String_($fullPath))
                    ,Node\Expr\Include_::TYPE_REQUIRE
                ), ['startTokenPos'=>$nodes->getStartTokenPos(),'endTokenPos'=> $nodes->getEndTokenPos()]
            );
            $nodes->stmts[]  = $expression;
            $originNode = $nodes->getAttribute("origNode");
            $originNode->stmts[] = $expression;
        }

        return $nodes;
    }

    public function handleAfterTravers(&$nodes,&$mFuncAr)
    {

        return $nodes;
    }

    function handlerUseNode(&$nodes)
    {
        // do nothing
    }
}
