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


use PhpParser\BuilderFactory;
use PhpParser\Node;

class GenerateBIHelper
{
    private $_reflectinst;
    private $_stmArgsNode =[];
    private $_factory;
    private $_args = null;
    public function __construct($reflectinst,$factory)
    {
        $this->_reflectinst = $reflectinst;
        $this->_factory = $factory;
        assert($this->_factory instanceof BuilderFactory);
    }

    public function getStmParamsDefine()
    {
        foreach ($this->_reflectinst->getParameters() as $param)
        {
            $pNode = $this->makeParam($param);

            if($param->isPassedByReference())
                $pNode->byRef = true;
            $this->_stmArgsNode[] = $pNode;
        }
        return $this->_stmArgsNode;;
    }

    private function makeParam($param)
    {
        if($param->isArray()){
            return $this->makeArrayParam($param);
        }else{
            return $this->makeOtherParam($param);
        }
    }

    private function makeArrayParam($param)
    {
//        $node = $this->_factory->param($param->getName())->setType('array');
        $node = new Node\Param($param->name);
        $node->type = 'array';
//        if ($param->isVariadic())
//            $node->variadic = true;
        if ($param->isOptional())
            $node->default = new Node\Expr\ConstFetch(new Node\Name('null'));
        if($param->isPassedByReference())
            $node->byRef = true;

        return $node;
    }

    private function makeOtherParam($param)
    {
        $node = new Node\Param($param->name);

//        if ($param->isVariadic())
//            $node->variadic = true;

        if($param->isOptional())
            $node->default = new Node\Expr\ConstFetch(new Node\Name('null'));

        if($param->isPassedByReference())
            $node->byRef = true;

        return $node;
    }

    public function getArrayParams()
    {
        if($this->_args){
            return $this->_args;
        }
        $vars =[];
        foreach ($this->_reflectinst->getParameters() as $param)
        {
            $name = $param->getName();

            $arItemNode = new Node\Expr\ArrayItem(new Node\Expr\Variable($name));

            if($param->isPassedByReference())
                $arItemNode->byRef = true;
            $vars[] =$arItemNode;
        }

        $this->_args  = new Node\Expr\Array_($vars);
        return $this->_args;
    }
}