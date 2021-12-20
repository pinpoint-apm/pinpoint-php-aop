<?php declare(strict_types=1);

namespace Pinpoint;

use Pinpoint\Common\PinpointDriver;
use Pinpoint\Plugins\PerRequestPlugins;
define('CLASS_PREFIX','Proxied_');

if(defined('PP_REQ_PLUGINS')  && class_exists(PP_REQ_PLUGINS)){
    $plugins = PP_REQ_PLUGINS;
    $plugins::instance();
}else{
    PerRequestPlugins::instance();
}

PinpointDriver::getInstance()->start();
