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
 * Time: 10:29 AM
 */

namespace pinpoint\Common;

use PhpParser\ParserFactory;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\NodeVisitorAbstract;
use PhpParser\NodeTraverser;
use pinpoint\Common\PluginVisitor;


class PluginParser
{
    private $clArray;


    private $namespace;
    private $className;
    private $pluginsFile;

    const BEFORE=0x1;
    const END=0x2;
    const EXCEPTION=0x4;
    const ALL=0x7;
    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * user could merge all Plugins, if duplicate, warning the innocent user
     * @return mixed
     */
    public function getClArray()
    {
        return $this->clArray;
    }

    /**
     * @param mixed $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return mixed
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @param mixed $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

    public function __construct($classFile,&$clArray)
    {
        assert(file_exists($classFile));
        $this->pluginsFile = $classFile;
        $this->clArray = &$clArray;
        $this->run();
    }

    public function run()
    {
        // todo , need add php5? php7 include php5 ?
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $nodes = $parser->parse(file_get_contents($this->pluginsFile));

        // find np and cl

        $nodeTraverser = new NodeTraverser;
        $nodeTraverser->addVisitor(new PluginVisitor($this));
        $nodeTraverser->traverse($nodes);
    }

    public function insertFunc($funcName, $mode)
    {
        $split = stripos ($funcName,'::');
        $uCl = substr($funcName,0,$split);
        $uFunc = substr($funcName,$split+2);

        /// not Internal func
        if(!array_key_exists($uCl,$this->clArray))
        {
            //  Cl = APP\Foo
            //  func = open
            $this->clArray[$uCl] = array( $uFunc =>
                    array($mode,$this->namespace,$this->className));
        }elseif (!array_key_exists($uFunc,$this->clArray[$uCl]))
        {
            $this->clArray[$uCl][$uFunc]= array($mode,$this->namespace,$this->className);
        }
        else {
            // when user tears the plugins, that only works on  $mode
            $this->clArray[$uCl][$uFunc][0] |= $mode;
        }
    }

}
