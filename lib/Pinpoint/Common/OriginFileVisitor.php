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

namespace Pinpoint\Common;

use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;

class OriginFileVisitor
{
    private $traverser;
    private $phpFileParser;
    public function __construct()
    {
        $this->traverser = new NodeTraverser();
        $this->phpFileParser = (new ParserFactory())->create(ParserFactory::ONLY_PHP7);
    }

    public function runAllVisitor(string $fullPath, AspectClassHandle $classHandler)
    {
        $this->traverser->addVisitor($this->getVisitor($classHandler));
        $code = file_get_contents($fullPath);
        $stmts = $this->phpFileParser->parse($code);
        $this->traverser->traverse($stmts);
    }

    private function getVisitor(AspectClassHandle $classHandler)
    {
        // $classPrefix = "";
        $visitors = [];
        if (!empty($classHandler->getMethodJoinPoints())) {
            Logger::Inst()->debug("found methodJoinPoints ");
            // $classPrefix = CLASS_PREFIX;
            $visitors[] = new GenProxyClassTemplateHelper($classHandler);
        }

        // $visitors[] = new GenOriginClassTemplateHelper($classHandler, $classPrefix);
        $codeVisitor = new CodeVisitor($visitors);
        return $codeVisitor;
    }
}
