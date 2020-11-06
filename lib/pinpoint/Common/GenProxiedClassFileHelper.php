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
    protected $suffix_use=[];
    public $mAopFuncInfo=[];

    public function __construct($fullFile,string $prefix,array $naming=[],array $aopFuncInfo=[])
    {
        parent::__construct($prefix);

        $this->orgDir = dirname($fullFile);
        $this->mAopFuncInfo = $aopFuncInfo;
        $this->orgFile = $fullFile;
        if(!empty($naming)){
            $this->t_covertCls  = $naming['classCall'];
            $this->t_covertFuns = $naming['funCall'];
        }
    }

    private function getRealNp($node)
    {
        assert($node instanceof Node\Name);
        if($node instanceof Node\Name\FullyQualified)         // Use directly access
        {
            return $node->toString();
        }else{    // Use namespace suggestion
            $prefixNm = $node->getFirst();
            if(isset($this->suffix_use[$prefixNm])){
                $prefix = $this->suffix_use[$prefixNm];
                $nm =  $prefix."\\".$node->toString();
                return $nm;
            }
            else{
                return $node->toString();
            }
        }
    }


    public function renderClassName(&$node,$filer)
    {
        $classFullName = $this->getRealNp($node);

        if(isset( $filer[$classFullName]))
        {
            $newName =$filer[$classFullName];
            return new Node\Name\FullyQualified($newName);
        }
        return $node;

    }

    public function renderFunName(&$node,$filer)
    {
        $classFullName = $node->toString();
        if(isset( $filer[$classFullName]))
        {
            $newName =$filer[$classFullName];
            return new Node\Name\FullyQualified($newName);
        }
        return $node;
    }

    public function handleEnterFuncCall(&$node)
    {
        assert($node instanceof Node\Expr\FuncCall);
        if($node->name instanceof Node\Expr\Variable){
            // not support anonymous function
        }else{
            $node->name =  $this->renderFunName($node->name,$this->t_covertFuns);
        }
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
        $this->myLoaderName = $this->className;

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
        $this->myLoaderName = $this->traitName;
    }


    public function handleLeaveMethodNode(&$node)
    {
        $func = trim( $node->name->toString());
        if(!array_key_exists($func,$this->mAopFuncInfo))
        {
            return ;
        }

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

    public function handleAfterTravers(&$nodes)
    {
        return $nodes;
    }

    function handlerUseUseNode(&$node)
    {
        assert($node instanceof  Node\Stmt\UseUse);

        // parse use A/B/C as ABC;
        if($node->alias){
            $aliasNm = $node->alias->name;
            $appendCl =[];
            $name = trim($node->name->toString(),"\ \\");
            foreach ($this->t_covertCls as $clName =>$value)
            {
                if(strpos($clName,$name) === 0)
                {
                    $np = str_replace($name ,$aliasNm,$clName);
                    $appendCl[$np] = $value;
                }
            }
            if(!empty($appendCl))
            {
                $this->t_covertCls += $appendCl;
            }
            return ;
        }

        // parse use A/B/C, but the nm_ is A/B/C/D
        $suffixNm = $node->name->getLast();
        $this->suffix_use[$suffixNm] = trim($node->name->slice(0,-1)->toString(),"\ \\");
//        print_r($this->suffix_use);
    }

    function handlerUseNode(&$node)
    {
        //rename the nodes
        assert($node instanceof Node\Stmt\Use_);
        $type = $node->type;
        if($type == Node\Stmt\Use_::TYPE_CONSTANT){
            return ;
        }

        // replace the exactly match
        // use A/B/C; -> Plugins/A/B/C
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
