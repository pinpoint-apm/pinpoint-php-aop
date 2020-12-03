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

namespace pinpoint\Common;

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

    public function runAllVisitor(string $fullPath,array $aopFuncInfo=[], array $naming=[])
    {
        if(!file_exists($fullPath))
        {
            throw new \Exception("$fullPath not found");
        }

        $this->traverser->addVisitor($this->getVisitor($fullPath,$aopFuncInfo,$naming));
        $code = file_get_contents($fullPath);
        $stmts = $this->phpFileParser->parse($code);
        $this->traverser->traverse($stmts);

    }

    private function getVisitor(string& $fullPath,array& $aopFuncInfo=[], array& $naming=[])
    {
        if(empty($aopFuncInfo)){
            $proxyClassFile = new GenProxiedClassFileHelper($fullPath,"",$naming);
            $codeVisitor = new NpCoderVisitor($proxyClassFile);
        }else{
            $proxyClassFile = new GenProxiedClassFileHelper($fullPath,CLASS_PREFIX,$naming,$aopFuncInfo);
            $originClassFile =  new GenOriginClassFileHelper($aopFuncInfo,CLASS_PREFIX);
            $codeVisitor = new CodeVisitor($originClassFile ,$proxyClassFile);
        }
        return $codeVisitor;
    }

}
