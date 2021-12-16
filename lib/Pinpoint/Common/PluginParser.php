<?php declare(strict_types=1);
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

namespace Pinpoint\Common;

use PhpParser\ParserFactory;
use PhpParser\NodeTraverser;


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
        $clsFullName = substr($funcName,0,$split);
        $methodName = substr($funcName,$split+2);

        /// not Internal func
        if(!array_key_exists($clsFullName,$this->clArray))
        {
            //  Cl = APP\Foo
            //  func = open
            $this->clArray[$clsFullName] = array( $methodName =>
                    array($mode,$this->namespace,$this->className));
        }elseif (!array_key_exists($methodName,$this->clArray[$clsFullName]))
        {
            $this->clArray[$clsFullName][$methodName]= array($mode,$this->namespace,$this->className);
        }
        else {
            // when user tears the plugins, that only works on  $mode
            $this->clArray[$clsFullName][$methodName][0] |= $mode;
        }
    }

}
