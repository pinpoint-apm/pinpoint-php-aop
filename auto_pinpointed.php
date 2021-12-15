<?php

namespace pinpoint;
use pinpoint\Common\RenderAopClass;
use pinpoint\Common\PinpointDriver;

//$classMap = new RenderAopClass();
//if(defined('USER_DEFINED_CLASS_MAP_IMPLEMENT'))
//{
//    $name = USER_DEFINED_CLASS_MAP_IMPLEMENT;
//    global $classMap;
//    $classMap = new $name();
//    assert($classMap instanceof RenderAopClass);
//}

define('CLASS_PREFIX','Proxied_');

PinpointDriver::getInstance()->start();

if(defined('PP_REQ_PLUGINS')  && class_exists(PP_REQ_PLUGINS)){
    $plugins = PP_REQ_PLUGINS;
    $plugins::instance();
}
