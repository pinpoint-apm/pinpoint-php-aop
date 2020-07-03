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
 * Date: 2/13/19
 * Time: 4:41 PM
 */

namespace pinpoint\Common;

use pinpoint\Common\ClassFile;
use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Node;
use pinpoint\Common\PluginParser;

class GenOriginClassFileHelper extends ClassFile
{
    private $factory;

    private $classNode; // class { }
    private $traitNode;

    private $extendTraitName;

    private $useBlockAr = [];

    private $trailUseAsArray=[];

    private $handleMethodNodeLeaveFunc = '';
    private $handleEndTraversFunc='';

    public function __construct($prefix = 'Proxied_')
    {
        parent::__construct($prefix);
        $this->factory= new BuilderFactory();

        $this->classNode = null;
        $this->traitNode = null;
    }


    public function handleEnterNamespaceNode(&$node)
    {
        parent::handleEnterNamespaceNode($node);

    }

    public function handleEnterClassNode(&$node)
    {
        assert($node instanceof Node\Stmt\Class_);
        parent::handleEnterClassNode($node);

        $extendClass = $this->prefix.$node->name;
        $this->classNode  = $this->factory->class(trim($node->name))->extend($extendClass);
        $this->useBlockAr[$this->namespace.'\\'.$extendClass] = null;

        switch($node->flags) {
            case Node\Stmt\Class_::MODIFIER_FINAL:
                $this->classNode->makeFinal();
                break;
            case Node\Stmt\Class_::MODIFIER_ABSTRACT:
                $this->classNode->makeAbstract();
                break;
           default:
                break;
        }

        $this->handleMethodNodeLeaveFunc = 'handleClassLeaveMethodNode';
        $this->handleEndTraversFunc      = 'handleAfterTraversClass';
    }

    public function handleEnterTraitNode(&$node)
    {
        assert($node instanceof Node\Stmt\Trait_);
        parent::handleEnterTraitNode($node);
        $this->traitNode  = $this->factory->trait(trim($node->name));
        $this->extendTraitName = $this->prefix.$node->name;
        $this->handleMethodNodeLeaveFunc = 'handleTraitLeaveMethodNode';
        $this->handleEndTraversFunc   = 'handleAfterTraversTrait';
    }


    public static function convertParms2RefArray($params)
    {
        assert(is_array($params));

        $items =[];
        foreach ($params as $param)
        {
            $var = new Node\Expr\Variable($param->name);
            $item = new Node\Expr\ArrayItem($var,NULL,true);
            $items[] = $item;
        }
        $arNode =new  Node\Expr\Array_($items);

        return $arNode;
    }

    public static function convertParamsName2Arg($params)
    {
        assert(is_array($params));

        $args = [];

        foreach ($params as $param)
        {
            assert($param instanceof Node\Param);

            $var = new Node\Expr\Variable($param->name);
            $arg = new Node\Arg($var);

            $args [] = $arg;
        }

        return  $args;
    }


    public function handleClassLeaveMethodNode(&$node,&$info)
    {
        /// todo this func looks ugly

        assert($node instanceof Node\Stmt\ClassMethod);

        list($mode, $namespace, $className) = $info;

        $np = $namespace . '\\' . $className;

        if(!in_array($np,$this->useBlockAr)){
            $this->useBlockAr[$np] =null;
        }

        // foo_1
        $thisFuncName = $node->name;

        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Method());

        // public function funcName(){}
        $thisMethod = $this->factory->method($thisFuncName);

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PUBLIC) {
            $thisMethod->makePublic();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PRIVATE) {
            $thisMethod->makeProtected();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_ABSTRACT) {

            $thisMethod->makeAbstract();
        }

        if($node->flags & Node\Stmt\Class_::MODIFIER_FINAL){
            $thisMethod->makeFinal();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PROTECTED) {
            $thisMethod->makeProtected();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_STATIC) {
            $thisMethod->makeStatic();
            $selfVar = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('null')));
        }else{
            $selfVar = new Node\Arg(new Node\Expr\Variable('this'));
        }
        
        $thisMethod->addParams($node->params);
        if($node->returnType){
            $thisMethod->setReturnType($node->returnType);
        }
        
        /// $refArgs = [&$a, &$b, &$v, &$d];
        $assignNode = new Node\Expr\Assign(new Node\Expr\Variable('refArgs'),GenOriginClassFileHelper::convertParms2RefArray($node->params));
        $thisMethod->addStmt($assignNode);

        /// $var = new CommonPlugins(__FUNCTION__,self,$refArgs);
        $varName = $className.'_'.$thisFuncName.'_var';
        $retName = $className.'_'.$thisFuncName.'_ret';
       
        $pluginArgs = [$funcVar,$selfVar,new Node\Arg(new Node\Expr\Variable('refArgs'))];

        $newPluginsStm = new Node\Expr\Assign(new Node\Expr\Variable($varName),
            new Node\Expr\New_(new Node\Name($className), $pluginArgs));

        $thisMethod->addStmt($newPluginsStm);
        // $var = null;
        $newVar = new Node\Expr\Assign(new Node\Expr\Variable($retName),
            new Node\Expr\ConstFetch(new Node\Name('null')));
        $thisMethod->addStmt($newVar);

        $tryBlock = [];
        $catchNode = [];

        if ($mode & PluginParser::BEFORE)
        {
            // $var->onBefore();
            $tryBlock[] =
                new Node\Expr\MethodCall(new Node\Expr\Variable($varName), "onBefore");
        }

        if ($this->hasRet) {
            /// $ret = paraent::$thisFuncName();
            $tryBlock[] =new Node\Expr\Assign(
                new Node\Expr\Variable($retName),
                new Node\Expr\StaticCall(new Node\Name("parent"),
                    "$thisFuncName",
                    GenOriginClassFileHelper::convertParamsName2Arg($node->params)));

            /// $var->onEnd($ret);
            if($mode & PluginParser::END)
            {
                $tryBlock[] =
                    new Node\Expr\MethodCall(
                        new Node\Expr\Variable($varName),
                        "onEnd",
                        [new Node\Expr\Variable($retName)]
                );
            }

            /// return $var;
            $tryBlock[] = new Node\Stmt\Return_(new Node\Expr\Variable($retName));

        } else {
            /// paraent::$thisFuncName();

            $tryBlock[] = new Node\Expr\StaticCall(
                new Node\Name("parent")
                , $thisFuncName,
                GenOriginClassFileHelper::convertParamsName2Arg($node->params));

            /// $var->onEnd($ret);
            if($mode & PluginParser::END)
            {
                $tryBlock[] =  new Node\Expr\MethodCall(
                        new Node\Expr\Variable($varName),
                        "onEnd",
                        [new Node\Expr\Variable($retName)]
                );
            }
        }

        $expArgs = [];
        $expArgs[] = new Node\Arg(new Node\Expr\Variable('e')) ;

        if ($mode & PluginParser::EXCEPTION) {

            $catchBlock[] =
                new Node\Expr\MethodCall(new Node\Expr\Variable($varName),
                    "onException",$expArgs);

        }

        $catchBlock[] = new Node\Stmt\Throw_(new Node\Expr\Variable("e"));

        $catchNode[] = new Node\Stmt\Catch_([new Node\Name('\Exception')],
            'e',
            $catchBlock);

        $tryCatchFinallyNode = new Node\Stmt\TryCatch($tryBlock,$catchNode);

        $thisMethod->addStmt($tryCatchFinallyNode);

        $this->classNode->addStmt($thisMethod);
    }

    public function handleTraitLeaveMethodNode(&$node,&$info)
    {
        /// todo this func looks ugly

        /// - check use , add  use Proxied_Foo { }
        /// - insert alias use Proxied_Foo::xxx as Foo_xxxx
        /// - new function xxxx

        assert($node instanceof Node\Stmt\ClassMethod);

        list($mode, $namespace, $className) = $info;

        // foo_1
        $thisFuncName = $node->name;

        $np = $namespace . '\\' . $className;
        // use traitTestPlugi;
        if(!in_array($np,$this->useBlockAr)){
            $this->useBlockAr[$np] = null;
        }

        // $this->extendTraitName::$thisFuncName as $this->extendTraitName_$thisFuncName;
        $this->trailUseAsArray[] = $thisFuncName;
        $extendMethodName = $this->extendTraitName.'_'.$thisFuncName;


        $funcVar = new Node\Arg(new Node\Scalar\MagicConst\Method());

        // public function funcName(){}
        $thisMethod = $this->factory->method($thisFuncName);

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PUBLIC) {
            $thisMethod->makePublic();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PRIVATE) {
            $thisMethod->makePrivate();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_ABSTRACT) {

            $thisMethod->makeAbstract();
        }

        if($node->flags & Node\Stmt\Class_::MODIFIER_FINAL){
            $thisMethod->makeFinal();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_PROTECTED) {
            $thisMethod->makeProtected();
        }

        if ($node->flags & Node\Stmt\Class_::MODIFIER_STATIC) {
            $thisMethod->makeStatic();
            $selfVar = new Node\Arg(new Node\Expr\ConstFetch(new Node\Name('null')));
        }else{
            $selfVar = new Node\Arg(new Node\Expr\Variable('this'));
        }

        /// $refArgs = [&$a, &$b, &$v, &$d];
        $assignNode = new Node\Expr\Assign(new Node\Expr\Variable('refArgs'),GenOriginClassFileHelper::convertParms2RefArray($node->params));
        $thisMethod->addStmt($assignNode);

        $pluginArgs  = [$funcVar,$selfVar,new Node\Arg(new Node\Expr\Variable('refArgs'))];

        $thisMethod->addParams($node->params);
        if($node->returnType){
            $thisMethod->setReturnType($node->returnType);
        }

        $varName = $className.'_'.$thisFuncName.'_var';
        $retName = $className.'_'.$thisFuncName.'_ret';

        /// $var = new CommonPlugins(__FUNCTION__,self,$p);
        $newPluginsStm = new Node\Expr\Assign(new Node\Expr\Variable($varName),
            new Node\Expr\New_(new Node\Name($className), $pluginArgs));

        $thisMethod->addStmt($newPluginsStm);
        // $var = null;
        $newVar = new Node\Expr\Assign(new Node\Expr\Variable($retName),
            new Node\Expr\ConstFetch(new Node\Name('null')));
        $thisMethod->addStmt($newVar);

        $tryBlock = [];
        $catchNode = [];

        if ($mode & PluginParser::BEFORE)
        {
            // $var->onBefore();
            $tryBlock[] =
                new Node\Expr\MethodCall(new Node\Expr\Variable($varName), "onBefore");
        }

        if ($this->hasRet) {
            $tryBlock[] = new Node\Expr\Assign(
                new Node\Expr\Variable($retName),
                new Node\Expr\MethodCall(
                    new Node\Expr\Variable("this"),
                    $extendMethodName,
                    GenOriginClassFileHelper::convertParamsName2Arg($node->params)));

            /// $var->onEnd($ret);
            if($mode & PluginParser::END)
            {
                $tryBlock[] =
                    new Node\Expr\MethodCall(
                        new Node\Expr\Variable($varName),
                        "onEnd",
                        [new Node\Expr\Variable($retName)]
                );
            }

            /// return $var;
            $tryBlock[] = new Node\Stmt\Return_(new Node\Expr\Variable($retName));

        } else {
            /// $this->$thisFuncName();
            $tryBlock[] =  new Node\Expr\MethodCall(
                new Node\Expr\Variable("this"),
                   $extendMethodName,
                GenOriginClassFileHelper::convertParamsName2Arg($node->params));

            /// $var->onEnd($ret);
            if($mode & PluginParser::END)
            {
                $tryBlock[] = new
                    Node\Expr\MethodCall(
                        new Node\Expr\Variable($varName),
                        "onEnd",
                        [new Node\Expr\Variable($retName)]
                    );
            }
        }

        $expArgs = [];
        $expArgs[] = new Node\Arg(new Node\Expr\Variable('e')) ;

        if ($mode & PluginParser::EXCEPTION) {

            $catchBlock[] =
                new
                Node\Expr\MethodCall(new Node\Expr\Variable($varName),
                    "onException",$expArgs);

        }

        $catchBlock[] = new Node\Stmt\Throw_(new Node\Expr\Variable("e"));

        $catchNode[] = new Node\Stmt\Catch_([new Node\Name('\Exception')],
            'e',
            $catchBlock);

        $tryCatchFinallyNode = new Node\Stmt\TryCatch($tryBlock,$catchNode);

        $thisMethod->addStmt($tryCatchFinallyNode);

        $this->traitNode->addStmt($thisMethod);

    }

    public function handleLeaveMethodNode(&$node,&$info)
    {
        call_user_func_array([$this,$this->handleMethodNodeLeaveFunc],[&$node,&$info]);
    }




    public function handleAfterTraversClass(&$nodes,&$mFuncAr)
    {
        $useNodes = [];

        $this->useBlockArToNodes($useNodes);

        $this->fileNode = $this->factory->namespace($this->namespace);

        if(count($useNodes) > 0){

            $this->fileNode->addStmts($useNodes);
        }

        $this->fileNode->addStmt($this->classNode);

        $this->fileName = $this->className;

        return $this->fileNode->getNode();
    }

    private function useBlockArToNodes(&$stmNode)
    {

        foreach ($this->useBlockAr as $useName=>$useAlias){

            if($useAlias){ // the second must be alias : use class as foo
                $node = $this->factory->use($useName)->as($useAlias);
            }else {//== 1
                $node = $this->factory->use($useName);
            }

            $stmNode[] = $node;
        }
    }

    public function handleAfterTraversTrait(&$nodes,&$mFuncAr)
    {
        $useNodes = [];
        $this->useBlockArToNodes($useNodes);

        // use Proxied_Foo{}
        $traitUse = new Node\Stmt\TraitUse([new Node\Name($this->extendTraitName)]);

        foreach ($this->trailUseAsArray as $alias)
        {
            // $extendMethodName::thisfuncName as $this->extendTraitName.'_'.$thisFuncName;
//            $useTraitNode->with($this->factory->traitUseAdaptation($this->extendTraitName,$alias)->as($this->extendTraitName.'_'.$alias));

            $traitUse->adaptations []= new Node\Stmt\TraitUseAdaptation\Alias(new Node\Name($this->extendTraitName),$alias,null,$this->extendTraitName.'_'.$alias);

        }

        $this->traitNode->addStmt($traitUse) ;

        $this->fileNode = $this->factory->namespace($this->namespace)
            ->addStmts($useNodes)
            ->addStmt($this->traitNode);

        $this->fileName = $this->traitName;

        return $this->fileNode->getNode();
    }

    public function handleAfterTravers(&$nodes,&$mFuncAr)
    {
        return call_user_func_array([$this,$this->handleEndTraversFunc],[&$nodes,&$mFuncAr]);
    }

    function handleLeaveNamespace(&$nodes)
    {
        // do nothing
    }

    function handlerUseNode(&$nodes)
    {
        assert($nodes instanceof Node\Stmt\Use_);
        foreach ($nodes->uses as $uses)
        {
//            $block= array($uses->name->toString());
//            if(isset($uses->alias)){
//                $block[]= $uses->alias->name;
//            }
//            var_dump($uses->name->toString());
            $this->useBlockAr[$uses->name->toString()] = $uses->alias ?  $uses->alias : null;
        }

    }
}
