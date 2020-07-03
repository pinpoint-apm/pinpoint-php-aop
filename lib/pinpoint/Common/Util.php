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

/**
 * User: eeliu
 * Date: 2/1/19
 * Time: 4:21 PM
 */

namespace pinpoint\Common;
use Composer\Autoload\ClassLoader;

use PhpParser\BuilderFactory;
use PhpParser\PrettyPrinter;
use PhpParser\Node;
class Util
{
//    private static $origin_class_loader;
    const StartWith = '@hook:';
//    const StartWith = '@hook:';
    const U_Method= 1;
    const U_Function= 2;

    /** locate a class (via vendor aop)
     * @param $class
     * @return bool|string
     */
    public static function findFile($class)
    {

        $splLoaders = spl_autoload_functions();
        $address = null;
        foreach ($splLoaders as $loader) {

            if (is_array($loader) && $loader[0] instanceof ClassLoader) {
                $address = $loader[0]->findFile($class);
            }
            elseif (is_array($loader) && is_string($loader[0])){
                if(method_exists($loader[0],'findFile')){
                    $address= call_user_func("$loader[0]::findFile",$class);

                }
            }

            if($address){
                return realpath($address);
            }
        }
        
        return false;
    }

    public static function parseUserFunc($str)
    {
        preg_match_all('#(?<=@hook:).*#', $str,$matched);

        if($matched){
            $func = [];
            foreach ($matched[0] as $str){
                $func =array_merge($func , preg_split("# |\|#",$str,-1,PREG_SPLIT_NO_EMPTY));
            }
            return $func;
        }

        return [];
    }

    public static function flushStr2File(&$context, $fullPath)
    {
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($fullPath, $context);
    }

    public static function isBuiltIn($name)
    {
        if (strpos($name, "\\") === 0) //build-in
        {
            if (strpos($name, "::") > 0){
                return static::U_Method;
            }else{
                return static::U_Function;
            }
        }

        return null;
    }

}
