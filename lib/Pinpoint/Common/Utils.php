<?php

declare(strict_types=1);
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

namespace Pinpoint\Common;

class Utils
{
    static $CLS_DIR;
    static $U_INDEX_FILE_PATH;
    static $U_INDEX_PHP;

    public static function saveObj(&$context, $fullPath)
    {
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($fullPath, $context);
    }

    /**
     * @deprecated
     */
    public static function scanDir($dir, $pattern, &$tree)
    {
        foreach (glob($dir . '/*') as $loc) {
            if (is_dir($loc)) {
                static::scanDir($loc, $pattern, $tree);
            } elseif (preg_match($pattern, $loc)) {
                $tree[] = realpath($loc);
            }
        }
    }

    public static function checkCacheReady(): bool
    {
        $cachePath = static::$U_INDEX_PHP;
        Logger::Inst()->debug("cachePath:'$cachePath'");
        return file_exists($cachePath);
    }

    public static function loadClassMap(): array
    {
        if (file_exists(static::$U_INDEX_PHP)) {
            return include_once static::$U_INDEX_PHP;
        } else {
            return NULL;
        }
    }

    public static function saveClassMap(array $cls)
    {
        $genClass = new GenClassIndexMap($cls);
        $genClass->save(static::$U_INDEX_PHP);
    }

    /**
     * @deprecated
     */
    public static function loadCachedClass(): array
    {
        if (file_exists(static::$U_INDEX_FILE_PATH)) {
            return unserialize(file_get_contents(static::$U_INDEX_FILE_PATH));
        } else {
            return null;
        }
    }

    /**
     * @deprecated
     */
    public static function saveCachedClass(array $class)
    {
        $context = serialize($class);
        static::saveObj($context, static::$U_INDEX_FILE_PATH);
        $size = sizeof($class);
        Logger::Inst()->debug("saveCachedClass size= '$size'");
    }
}
if (defined('AOP_CACHE_DIR')) {
    Utils::$CLS_DIR = AOP_CACHE_DIR;
} else {
    Utils::$CLS_DIR = sys_get_temp_dir() . '/.cache';
}

Utils::$U_INDEX_FILE_PATH = Utils::$CLS_DIR . '/.__class_index_table';
Utils::$U_INDEX_PHP = Utils::$CLS_DIR . '/.__class_index.php';