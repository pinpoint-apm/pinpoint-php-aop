<?php declare(strict_types=1);

namespace pinpoint;
use pinpoint\Common\PinpointDriver;

define('CLASS_PREFIX','Proxied_');

PinpointDriver::getInstance()->start();

if(defined('PP_REQ_PLUGINS')  && class_exists(PP_REQ_PLUGINS)){
    $plugins = PP_REQ_PLUGINS;
    $plugins::instance();
}
