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
 * Time: 10:33 AM
 */

namespace Pinpoint\Common;

use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;

class CodeVisitor extends NodeVisitorAbstract
{

    public $visitors = [];

    protected $builtInAr = [];

    public function __construct(array $_visitors)
    {
        $this->visitors = $_visitors;
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            foreach ($this->visitors as $visitor) {
                assert($visitor instanceof  AbstractClassFile);
                $visitor->handleEnterNamespaceNode($node);
            }
        } elseif ($node instanceof Node\Stmt\Use_) {
            foreach ($this->visitors as $visitor) {
                $visitor->handlerUseNode($node);
            }
        } elseif ($node instanceof Node\Stmt\Class_) {
            if (empty($node->name->toString())) {
                throw new \Exception("can't AOP an anonymous class");
            }

            // $this->className = empty($this->classNamespace) ? ($node->name->toString()) : ($this->classNamespace . '\\' . $node->name->toString());

            foreach ($this->visitors as $visitor) {
                $visitor->handleEnterClassNode($node);
            }
        } elseif ($node instanceof Node\Stmt\Trait_) {
            if (empty($node->name->toString())) {
                throw new \Exception("can't AOP an anonymous trait");
            }
            // $this->className = empty($this->classNamespace) ? ($node->name->toString()) : ($this->classNamespace . '\\' . $node->name->toString());

            foreach ($this->visitors as $visitor) {
                $visitor->handleEnterTraitNode($node);
            }
        } elseif ($node instanceof Node\Stmt\ClassMethod) {
            foreach ($this->visitors as $visitor) {
                $visitor->handleClassEnterMethodNode($node);
            }
        } elseif ($node instanceof Node\Stmt\Return_) {
            foreach ($this->visitors as $visitor) {
                $visitor->markHasReturn($node);
            }
        } elseif ($node instanceof Node\Expr\Yield_) {
            foreach ($this->visitors as $visitor) {
                $visitor->markHasYield();
            }
        } elseif ($node instanceof Node\Expr\ClassConstFetch) {
            foreach ($this->visitors as $visitor) {
                $visitor->handleEnterClassConstFetch($node);
            }
        } elseif ($node instanceof  Node\Expr\New_) {
            foreach ($this->visitors as $visitor) {
                $visitor->handleEnterNew($node);
            }
        } elseif ($node instanceof Node\Expr\FuncCall) {
            foreach ($this->visitors as $visitor) {
                $visitor->handleEnterFuncCall($node);
            }
        }
    }


    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\ClassMethod) {
            foreach ($this->visitors as $visitor) {
                $visitor->handleLeaveMethodNode($node);
            }
        } elseif ($node instanceof Node\Scalar\MagicConst) {
            foreach ($this->visitors as $visitor) {
                $visitor->handleMagicConstNode($node);
            }
        } elseif ($node instanceof Node\Stmt\Namespace_) {
            foreach ($this->visitors as $visitor) {
                $visitor->handleLeaveNamespace($node);
            }
        } elseif ($node instanceof Node\Stmt\Class_) {
            foreach ($this->visitors as $visitor) {
                $visitor->handleLeaveClassNode($node);
            }
        } elseif ($node instanceof Node\Stmt\Trait_) {

            foreach ($this->visitors as $visitor) {
                $visitor->handleLeaveTraitNode($node);
            }
        }

        return $node;
    }

    public function afterTraverse(array $nodes)
    {
        foreach ($this->visitors as $visitor) {
            $visitor->handleAfterTraverse($nodes);
            $visitor->done();
        }
    }
}
