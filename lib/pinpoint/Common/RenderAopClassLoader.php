<?php
/*
 * User: eeliu
 * Date: 12/15/21
 * Time: 2:53 PM
 */

namespace pinpoint\Common;


class RenderAopClassLoader
{
    public static function loadClass($class):bool
    {
        $file = RenderAopClass::getInstance()->findFile($class);
        if($file!==''){
            require $file;
            return true;
        }
        return false;
    }

    public static function start()
    {
        spl_autoload_register(array_flip(static::loadClass),true,true);
    }
}