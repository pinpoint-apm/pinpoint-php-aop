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

use pinpoint\Common\GenProxiedClassFileHelper;
use pinpoint\Common\GenOriginClassFileHelper;
use pinpoint\Common\CodeVisitor;

use PhpParser\Lexer;
use PhpParser\Parser;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\BuilderFactory;
use pinpoint\Common\Util;

class OrgClassParse
{
    private $originFile;
    private $lexer;
    private $parser;
    private $traverser;
    private $printer;

    private $originClassNode;

    private $rawOrigStmts;

    public $classIndex = [];

    public $className;// app\foo\DBManager

    public $mFuncAr=[];

    public $proxiedClassFile;
    public $originClassFile;

    public $orgClassPath;
    public $shadowClassPath;


    public function __construct($fullPath, $info=null,array $naming=null)
    {
        assert(file_exists($fullPath));
        if($info)
        {
            $this->mFuncAr = $info;
        }

        $this->originFile = $fullPath;
        $this->lexer = new Lexer\Emulative([
            'usedAttributes' => [
                'comments',
                'startLine',
                'endLine',
                'startTokenPos',
                'endTokenPos',
            ],
        ]);

        $this->parser = new Parser\Php7($this->lexer, [
            'useIdentifierNodes' => true,
            'useConsistentVariableNodes' => true,
            'useExpressionStatements' => true,
            'useNopStatements' => false,
        ]);

        $this->traverser = new NodeTraverser();
        $this->traverser->addVisitor(new NodeVisitor\CloningVisitor());
        $this->traverser->addVisitor(new CodeVisitor($this));

        $this->printer = new PrettyPrinter\Standard();

        $this->proxiedClassFile = new GenProxiedClassFileHelper($fullPath,$naming);
        $this->originClassFile =  new GenOriginClassFileHelper(CLASS_PREFIX);

        $this->parseOriginFile();
    }

    protected function parseOriginFile()
    {
        $code = file_get_contents($this->originFile);

        $this->rawOrigStmts = $this->parser->parse($code);

        $this->originClassNode = $this->traverser->traverse($this->rawOrigStmts);

    }

    /// convert $node to file
    public function proxyFileNodeDoneCB($node, $fullName)
    {
        $fullPath = AOP_CACHE_DIR.'/'.str_replace('\\','/',$fullName).'.php';
        // try to keep blank and filenu
        $orgClassContext =  $this->printer->prettyPrintFile($node);

//        $orgClassContext = $this->printer->printFormatPreserving(
//            $node,
//         $this->rawOrigStmts,
//            $this->lexer->getTokens());

        Util::flushStr2File($orgClassContext,$fullPath);
        $this->classIndex[$fullName] = $fullPath;
    }

    /// convert $node to file
    public function orginFileNodeDoneCB(&$node, $fullName)
    {
        $fullPath = AOP_CACHE_DIR.'/'.str_replace('\\','/',$fullName).'.php';
        $context= $this->printer->prettyPrintFile($node);
        Util::flushStr2File($context,$fullPath);
        $this->classIndex[$fullName] = $fullPath;
    }


    public function generateAllClass()
    {
        /// ast to source
        return $this->classIndex;
    }
}
