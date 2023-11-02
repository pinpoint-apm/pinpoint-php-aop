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
 * Date: 2/14/19
 * Time: 11:36 AM
 */

namespace Pinpoint\Common;

use PhpParser\Node;
use PhpParser\PrettyPrinter;

/**
 * Class AbstractClassFile
 *
 * A abstract php-parse node
 *      namespace node
 *      use nodes
 *      class node
 *      required node
 *
 * @package pinpoint\Common
 */
abstract class AbstractClassFile
{
    public $appendingFile = array();

    public $newAstNode;

    public $npStr;

    public $className = ''; /// Foo\A Foo\B

    public $traitName = ''; /// trait Foo {}

    public $fileName = '';  /// output file Name

    public $myLoaderName = '';      /// output name

    public $classMethod;

    public $funcName; // only for __FUNCTION__

    protected $dir;

    public $hasRet;


    protected $_astPrinter;

    public $namespace = '';

    public function __construct()
    {
        $this->_astPrinter = new PrettyPrinter\Standard();
    }

    // public function getNode()
    // {
    //     return $this->node;
    // }

    public function handleEnterNamespaceNode($node)
    {
        assert($node instanceof Node\Stmt\Namespace_);
        $this->namespace = trim($node->name->toString());
    }

    public function handleEnterClassNode($node)
    {
        assert($node instanceof Node\Stmt\Class_);
        if ($this->namespace) {
            $this->className = trim($this->namespace . '\\' . $node->name->toString());
        } else {
            $this->className = trim($node->name->toString());
        }
    }

    public function handleEnterTraitNode(&$node)
    {
        assert($node instanceof Node\Stmt\Trait_);
        if ($this->namespace)
            $this->traitName = trim($this->namespace . '\\' . $node->name->toString());
        else
            $this->traitName = trim($node->name->toString());
    }

    public function handleClassEnterMethodNode(&$node)
    {
        assert($node instanceof Node\Stmt\ClassMethod);
        $this->funcName = $node->name->toString();
        $this->classMethod = $this->className . '::' . $this->funcName;
        $this->hasRet = false;
    }

    public function markHasReturn(&$node)
    {
        if (isset($node->expr)) {
            $this->hasRet = true;
        }
    }

    public function markHasYield()
    {
        $this->hasRet = true;
    }

    public function done()
    {
        $fullPath = AOP_CACHE_DIR . '/' . str_replace('\\', '/', $this->className) . '.php';
        $context = $this->_astPrinter->prettyPrintFile($this->newAstNode);
        MonitorClass::getInstance()->insertMapping($this->className, $fullPath);
        Logger::Inst()->debug("map/save new class '$this->className' to '$fullPath' ");
        Utils::saveObj($context, $fullPath);
    }

    abstract function handleAfterTraverse($nodes);
    abstract function handleLeaveNamespace($nodes);
    abstract function handlerUseNode($node);
    abstract function handleLeaveMethodNode($node);
    abstract function handleEnterClassConstFetch($node);
    abstract function handleEnterNew($node);
    abstract function handleEnterFuncCall($node);
    abstract function handleLeaveClassNode($node);
}
