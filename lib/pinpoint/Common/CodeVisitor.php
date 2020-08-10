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
use pinpoint\Common\GenRequiredBIFileHelper;

class CodeVisitor extends NodeVisitorAbstract
{

    protected $ospIns;
    private $curNamespace;
    private $curClass;

    protected $builtInAr = []; // curl_init PDO

    public function __construct($ospIns)
    {
        assert($ospIns instanceof OrgClassParse);
        $this->ospIns = $ospIns;
        $this->curClass = $ospIns->className;
    }

    public function enterNode(Node $node)
    {
        if($node instanceof Node\Stmt\Namespace_)
        {
            $this->curNamespace = $node->name->toString();
            /// set namespace
            $this->ospIns->originClassFile->handleEnterNamespaceNode($node);
            $this->ospIns->proxiedClassFile->handleEnterNamespaceNode($node);

            $reqFile = new GenRequiredBIFileHelper($this->curNamespace);
            foreach ($this->ospIns->mFuncAr as $key => $value)
            {
                $ret = Util::isBuiltIn($key);

                if($ret == Util::U_Method){
                    list($clName,$clMethod) = preg_split ("/[::|\\\]/",$key,-1,PREG_SPLIT_NO_EMPTY);
                    $this->builtInAr[] = $clName;
                    $reqFile->extendsMethod($clName,$clMethod,$value);
                }elseif ($ret == Util::U_Function){

                    list($funcName) = preg_split ("/[\\\]/",$key,-1,PREG_SPLIT_NO_EMPTY);
                    $this->builtInAr [] = $funcName ;
                    $reqFile->extendsFunc($funcName,$value);
                }else{
                    //do nothing
                }
            }

            $reqRelativityFile = str_replace('\\','/', $this->curClass).'_required.php';
            $reqFile->loadToFile(AOP_CACHE_DIR.$reqRelativityFile);
            $this->ospIns->requiredFile = AOP_CACHE_DIR.$reqRelativityFile;
            $this->ospIns->proxiedClassFile->addRequiredFile($reqRelativityFile);
        }
        elseif ($node instanceof Node\Stmt\Use_){
            $this->ospIns->proxiedClassFile->handlerUseNode($node);
            $this->ospIns->originClassFile->handlerUseNode($node);
        }
        elseif ($node instanceof Node\Stmt\Class_){

            $this->ospIns->originClassFile->handleEnterClassNode($node);
            $this->ospIns->proxiedClassFile->handleEnterClassNode($node);
        }
        elseif( $node instanceof Node\Stmt\Trait_){
            if( $this->curNamespace.'\\'.$node->name->toString() != $this->curClass)
            {
                // ignore uncared
                return NodeTraverser::DONT_TRAVERSE_CHILDREN;
            }

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

            $this->ospIns->proxiedClassFile->handleMagicConstNode($node);
        }elseif ($node instanceof Node\Stmt\Namespace_){
            return $this->ospIns->proxiedClassFile->handleLeaveNamespace($node);
        }
        elseif ($node instanceof Node\Stmt\Class_){

            $this->ospIns->proxiedClassFile->handleLeaveClassNode($node);

        }elseif ($node instanceof Node\Stmt\Trait_){

            $this->ospIns->proxiedClassFile->handleLeaveTraitNode($node);
        }
        elseif ($node instanceof Node\Stmt\UseUse){
            /// scene : use \PDO
            ///        replace \PDO to \Np\PDO
            if( in_array($node->name->toString(),$this->builtInAr))
            {
                $node->name   = new Node\Name($this->curNamespace.'\\'.$node->name->toString());
            }
            return $node;
        }
    }

    public function afterTraverse(array $nodes)
    {
        $node = $this->ospIns->proxiedClassFile->handleAfterTravers($nodes,
            $this->ospIns->mFuncAr);

        $this->ospIns->orgClassNodeDoneCB($node,$this->ospIns->proxiedClassFile->name);

        $node = $this->ospIns->originClassFile->handleAfterTravers($nodes,
            $this->ospIns->mFuncAr);

        $this->ospIns->shadowClassNodeDoneCB($node,$this->ospIns->originClassFile->fileName);

    }

}
