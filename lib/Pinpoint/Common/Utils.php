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
    const U_INDEX_FILE_PATH = AOP_CACHE_DIR . '/.__class_index_table';

    public static function saveObj(&$context, $fullPath)
    {
        $dir = dirname($fullPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($fullPath, $context);
    }


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
        $cachePath = static::U_INDEX_FILE_PATH;
        $useCache = defined('PINPOINT_USE_CACHE') && PINPOINT_USE_CACHE;
        Logger::Inst()->debug("cachePath:'$cachePath' useCache '$useCache' ");
        return (defined('PINPOINT_USE_CACHE') &&
            PINPOINT_USE_CACHE) &&
            file_exists(static::U_INDEX_FILE_PATH);
    }

    public static function loadCachedClass(): array
    {
        if (file_exists(static::U_INDEX_FILE_PATH)) {
            return unserialize(file_get_contents(static::U_INDEX_FILE_PATH));
        } else {
            return null;
        }
    }

    public static function saveCachedClass(array $class)
    {
        $context = serialize($class);
        static::saveObj($context, static::U_INDEX_FILE_PATH);
        $size = sizeof($class);
        Logger::Inst()->debug("saveCachedClass size= '$size'");
    }
}
