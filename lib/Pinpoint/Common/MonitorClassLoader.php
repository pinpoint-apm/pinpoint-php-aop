<?php

declare(strict_types=1);
/*
 * User: eeliu
 * Date: 12/15/21
 * Time: 2:53 PM
 */

namespace Pinpoint\Common;

class MonitorClassLoader
{
    private static $registered = false;
    private static $loader = [__CLASS__, 'loadClass'];
    public static function loadClass($class)
    {
        $file = MonitorClass::getInstance()->findFile($class);
        if ($file != '') {
            Logger::Inst()->debug("MonitorClassLoader::loadClass: '$file'", [__CLASS__]);
            require $file;
            return true;
        } else {
            return false;
        }
    }
    /**
     * spl_autoload_register MonitorClassLoader::loadClass
     */
    public static function start()
    {
        if (!self::$registered) {
            spl_autoload_register(self::$loader, true, true);
            self::$registered = true;
            Logger::Inst()->debug("register MonitorClassLoader");
        }
    }
}
