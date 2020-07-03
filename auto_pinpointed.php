<?php

namespace pinpoint;
use pinpoint\Common\AopClassMap;
use pinpoint\Common\PinpointDriver;

$classMap = null;
if(defined('USER_DEFINED_CLASS_MAP_IMPLEMENT'))
{
    $className = USER_DEFINED_CLASS_MAP_IMPLEMENT;
    $classMap = new $className();
    assert($classMap instanceof AopClassMap);
}else{
    $classMap = new AopClassMap();
}

PinpointDriver::getInstance()->init($classMap);

if(class_exists("\Plugins\PerRequestPlugins")){
    \Plugins\PerRequestPlugins::instance();
}
