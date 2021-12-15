<?php declare(strict_types=1);
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

class Util
{
    const U_Method= 1;
    const U_Function= 2;
    const U_INDEX_FILE_PATH = AOP_CACHE_DIR.'__class_index_table';

    /**
     * locate a class (via  VendorAdaptorClassLoader)
     * @param $class
     * @return bool|string
     */
    public static function findFile($class):string
    {
        $splLoaders = spl_autoload_functions();
        foreach ($splLoaders as &$loader) {

            if ( is_array($loader) && $loader[0] instanceof VendorAdaptorClassLoader) {
                $address = $loader[0]->findFile($class);
                if($address){
                    return realpath($address);
                }
            }else{
                throw new \Exception("unknown loader");
            }
        }
        
        return '';
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

    public static function saveObj(&$context, $fullPath)
    {
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($fullPath, $context);
    }


    public static function scanDir($dir,$pattern,&$tree)
    {
        foreach (glob($dir.'/*') as $loc)
        {
            if(is_dir($loc)){
                static::scanDir($loc,$pattern,$tree);
            }elseif (preg_match($pattern,$loc)){
                $tree[]=$loc;
            }
        }
    }

    public static function checkCacheReady():bool {
        return (defined('PINPOINT_USE_CACHE') &&
        stristr(PINPOINT_USE_CACHE,"YES") !== false ) &&
        file_exists(static::U_INDEX_FILE_PATH);
    }

    public static function loadCachedClass():array {
        if(file_exists(static::U_INDEX_FILE_PATH)){
            return unserialize(file_get_contents(static::U_INDEX_FILE_PATH));
        }else{
            return null;
        }
    }

    public static function saveCachedClass(array $class){
        $context = serialize($class);
        static::saveObj($context,static::U_INDEX_FILE_PATH);
    }


}