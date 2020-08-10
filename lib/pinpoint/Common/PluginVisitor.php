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
 * Time: 2:37 PM
 */

namespace pinpoint\Common;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node;
use pinpoint\Common\PluginParser;

class PluginVisitor extends NodeVisitorAbstract
{
    private $iParser;
    public function __construct($parser)
    {
        if( $parser instanceof PluginParser)
        {
            $this->iParser = $parser;
            return ;
        }
        throw new \Exception("illegal input");
    }

    ///$PluginsInfo => class
    private function loadCommentFunc(&$node,$mode)
    {
       foreach( $node->getComments() as &$doc)
       {
            $funArray = Util::parseUserFunc(trim($doc->getText()));

            foreach ($funArray as $func)
            {
                $this->iParser->insertFunc(trim($func),$mode);
            }
       }
    }

    public function enterNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Namespace_) {
            $this->iParser->setNamespace(trim($node->name->toString()));
        }
        elseif($node instanceof Node\Stmt\Class_) {
            $this->iParser->setClassName(trim($node->name->toString()));
            $this->loadCommentFunc($node, PluginParser::ALL);
        }
    }

    public function leaveNode(Node $node)
    {
        if($node instanceof Node\Stmt\ClassMethod)
        {
            $name = $node->name->toString();
            $node->getComments();
            switch($name)
            {
                case "onBefore":
                    $this->loadCommentFunc($node, PluginParser::BEFORE);
                    break;
                case "onEnd":
                    $this->loadCommentFunc($node, PluginParser::END);
                    break;
                case "onException":
                    $this->loadCommentFunc($node, PluginParser::EXCEPTION);
                    break;
                default:
                    // do nothing
            }
        }
    }
}