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
    protected $useBlockAr=[];
    protected $t_covertCls=[];
    protected $t_covertFuns=[];

    public function __construct($fullFile,array $naming=null)
    {
        parent::__construct(CLASS_PREFIX);

        $this->orgDir = dirname($fullFile);
        $this->orgFile = $fullFile;
        if($naming){
            $this->t_covertCls  = $naming['classCall'];
            $this->t_covertFuns = $naming['funCall'];
        }
    }

    public function renderClassName(&$node,$filer)
    {
        if($node instanceof Node\Name\FullyQualified) // \PDO()
        {
            $classFullName = $node->toString();
            if(isset( $filer[$classFullName]))
            {
                $newName =$filer[$classFullName];
                return new Node\Name\FullyQualified($newName);
            }
        }
        return $node;
    }

    public function handleEnterFuncCall(&$node)
    {
        assert($node instanceof Node\Expr\FuncCall);
        $node->name =  $this->renderClassName($node->name,$this->t_covertFuns);
        return $node;
    }


    public function handleEnterNew_(&$node)
    {
        assert($node instanceof Node\Expr\New_);
        $node->class =  $this->renderClassName($node->class,$this->t_covertCls);
        return $node;

    }
    public function handleEnterClassConstFetch(&$node)
    {
        assert($node instanceof Node\Expr\ClassConstFetch);
        $node->class =  $this->renderClassName($node->class,$this->t_covertCls);
        return $node;
    }


    /** rename the class Proxied_foo
     * @param $node
     */
    public function handleLeaveClassNode(&$node)
    {
        assert($node instanceof Node\Stmt\Class_);
        $className = $this->prefix.$node->name->toString();

        $node->name = new Node\Identifier($className);

        $this->className = empty($this->namespace) ? ($className): $this->namespace.'\\'.$className;
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

        $this->traitName = empty($this->namespace) ? ($className):($this->namespace.'\\'.$className);
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

    }

    public function handleMagicConstNode(&$node)
    {
        switch ($node->getName())
        {
            case '__FILE__':
                return new Node\Scalar\String_($this->orgFile);
            case '__DIR__':
                return new Node\Scalar\String_($this->orgDir);
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


    public function handleLeaveNamespace(&$nodes)
    {
        return $nodes;
    }

    public function handleAfterTravers(&$nodes,&$mFuncAr)
    {
        return $nodes;
    }

    function handlerUseUseNode(&$node)
    {
//        $fullName = trim($node->name->toString(),"\ \\");
//        if($node->type == Node\Stmt\Use_::TYPE_FUNCTION){
//            if(isset($this->t_funName[$fullName])){
//                $newName = new Node\Name($this->t_funName[$fullName]);
//                $node->name = $newName;
//            }
//        }
//        else{ // normal and unknow
//            if(isset($this->t_clName[$fullName])){
//                $newName = new Node\Name($this->t_clName[$fullName]);
//                $node->name = $newName;
//            }
//        }
    }

    function handlerUseNode(&$node)
    {
        //rename the nodes
        assert($node instanceof Node\Stmt\Use_);
        $type = $node->type;
        if($type == Node\Stmt\Use_::TYPE_CONSTANT){
            return ;
        }

        foreach ($node->uses as &$uses)
        {
            $fullName = trim($uses->name->toString(),"\ \\");
            if($type == Node\Stmt\Use_::TYPE_FUNCTION){
                if(isset($this->t_covertFuns[$fullName])){
                    $newName = new Node\Name($this->t_covertFuns[$fullName]);
                    $uses->name = $newName;
                }
            }
            else{ // normal and unknow
                if(isset($this->t_covertCls[$fullName])){
                    $newName = new Node\Name($this->t_covertCls[$fullName]);
                    $uses->name = $newName;
                }
            }
        }

    }
}
