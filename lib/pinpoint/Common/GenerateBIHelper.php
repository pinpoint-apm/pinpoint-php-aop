<?php
/**
 * Created by PhpStorm.
 * User: test
 * Date: 4/24/20
 * Time: 11:13 AM
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
                $pNode->makeByRef();
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
        $node = $this->_factory->param($param->getName())->setType('array');

        if ($param->isVariadic())
            $node->makeVariadic();
        elseif ($param->isOptional())
//            new Node\Name('null')
            $node->setDefault(null);

        if($param->isPassedByReference())
            $node->makeByRef();

        return $node;
    }

    private function makeOtherParam($param)
    {
        $node =  $this->_factory->param($param->getName());

        if ($param->isVariadic())
            $node->makeVariadic();
        elseif($param->isOptional())
            $node->setDefault(null);

        if($param->isPassedByReference())
            $node->makeByRef();

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