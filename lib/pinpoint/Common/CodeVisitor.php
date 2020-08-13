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
 * Time: 10:33 AM
 */

namespace pinpoint\Common;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use PhpParser\NodeTraverser;
//use pinpoint\Common\GenRequiredBIFileHelper;

class CodeVisitor extends NodeVisitorAbstract
{

    protected $ospIns;
    private $curNamespace;
    private $curName;

    protected $builtInAr = []; // curl_init PDO

    public function __construct($ospIns)
    {
        assert($ospIns instanceof OrgClassParse);
        $this->ospIns = $ospIns;
    }

    public function enterNode(Node $node)
    {
        if($node instanceof Node\Stmt\Namespace_)
        {
            $this->curNamespace = $node->name->toString();
            /// set namespace
            $this->ospIns->originClassFile->handleEnterNamespaceNode($node);
            $this->ospIns->proxiedClassFile->handleEnterNamespaceNode($node);
        }
        elseif ($node instanceof Node\Stmt\Use_){
            $this->ospIns->proxiedClassFile->handlerUseNode($node);
            $this->ospIns->originClassFile->handlerUseNode($node);
        }
        elseif ($node instanceof Node\Stmt\Class_){
            if(empty($node->name->toString())){
                throw new \Exception("can't AOP an anonymous class");
            }
            $this->curName = empty($this->curNamespace) ? ($node->name->toString()):($this->curNamespace.'\\'.$node->name->toString());
            $this->ospIns->originClassFile->handleEnterClassNode($node);
            $this->ospIns->proxiedClassFile->handleEnterClassNode($node);
        }
        elseif( $node instanceof Node\Stmt\Trait_){
            if(empty($node->name->toString())){
                throw new \Exception("can't AOP an anonymous trait");
            }
            $this->curName = empty($this->curNamespace) ? ($node->name->toString()):($this->curNamespace.'\\'.$node->name->toString());

            $this->ospIns->proxiedClassFile->handleEnterTraitNode($node);
            $this->ospIns->originClassFile->handleEnterTraitNode($node);
        }elseif ($node instanceof Node\Stmt\ClassMethod)
        {
            $this->ospIns->originClassFile->handleClassEnterMethodNode($node);
            $this->ospIns->proxiedClassFile->handleClassEnterMethodNode($node);
        }
        elseif ( $node instanceof Node\Stmt\Return_)
        {
            $this->ospIns->originClassFile->markHasReturn($node);
        }
        elseif ($node instanceof Node\Expr\Yield_){
            $this->ospIns->originClassFile->markHasYield($node);
        }
        elseif ($node instanceof Node\Expr\ClassConstFetch){
            return $this->ospIns->proxiedClassFile->handleEnterClassConstFetch($node);
        }elseif ($node instanceof  Node\Expr\New_){
            return $this->ospIns->proxiedClassFile->handleEnterNew_($node);
        }elseif ($node instanceof Node\Expr\FuncCall){
            return $this->ospIns->proxiedClassFile->handleEnterFuncCall($node);
        }
    }


    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassMethod){
            $func = trim( $node->name->toString());
            if(array_key_exists($func,$this->ospIns->mFuncAr))
            {
                $this->ospIns->originClassFile->handleLeaveMethodNode($node,$this->ospIns->mFuncAr[$func]);
                $this->ospIns->proxiedClassFile->handleLeaveMethodNode($node,$this->ospIns->mFuncAr[$func]);
                /// remove the func
                unset( $this->ospIns->mFuncAr[$func] );
            }
        }elseif ($node instanceof Node\Name\FullyQualified){
            // use Foo\Name replace \Name
            $name = $node->toString();
            if(! in_array($name,$this->builtInAr) ){
                return $node;
            }
            return $this->ospIns->proxiedClassFile->handleFullyQualifiedNode($node);
        }
        elseif ($node instanceof Node\Scalar\MagicConst){
            return $this->ospIns->proxiedClassFile->handleMagicConstNode($node);
        }elseif ($node instanceof Node\Stmt\Namespace_){
            return $this->ospIns->proxiedClassFile->handleLeaveNamespace($node);
        }
        elseif ($node instanceof Node\Stmt\Class_) {
            $this->ospIns->proxiedClassFile->handleLeaveClassNode($node);
        }elseif ($node instanceof Node\Stmt\Trait_){

            $this->ospIns->proxiedClassFile->handleLeaveTraitNode($node);
        }
        elseif ($node instanceof Node\Stmt\UseUse){
            /// scene : use \PDO
            ///        replace \PDO to \Np\PDO
//            if( in_array($node->name->toString(),$this->builtInAr))
//            {
//                $node->name   = new Node\Name($this->curNamespace.'\\'.$node->name->toString());
//            }

//            $this->ospIns->proxiedClassFile->handlerUseUseNode($node);


            return $node;
        }
    }

    public function afterTraverse(array $nodes)
    {
        $node = $this->ospIns->proxiedClassFile->handleAfterTravers($nodes,
            $this->ospIns->mFuncAr);

        $this->ospIns->proxyFileNodeDoneCB($node,$this->ospIns->proxiedClassFile->name);

        $node = $this->ospIns->originClassFile->handleAfterTravers($nodes,
            $this->ospIns->mFuncAr);

        $this->ospIns->orginFileNodeDoneCB($node,$this->ospIns->originClassFile->fileName);

    }

}
