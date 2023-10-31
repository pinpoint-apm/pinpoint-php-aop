<?php

declare(strict_types=1);
/*
 * User: eeliu
 * Date: 12/15/21
 * Time: 2:53 PM
 */

namespace Pinpoint\Common;

class RenderAopClassLoader
{
    public static function loadClass($class)
    {
        $file = RenderAopClass::getInstance()->findFile($class);
        if ($file != '') {
            require $file;
            return true;
        }
    }

    public static function start()
    {
        spl_autoload_register([__NAMESPACE__ . '\RenderAopClassLoader', 'loadClass'], true, true);
        Logger::Inst()->debug("register RenderAopClassLoader");
    }
}
