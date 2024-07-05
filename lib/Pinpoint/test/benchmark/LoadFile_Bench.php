<?php

namespace Pinpoint\test\benchmark;

define('AOP_CACHE_DIR', __DIR__);

use Pinpoint\Common\Utils;

$cls_map = __DIR__ . '/class_map.php';

// function create_index()
// {
//     global $cls_map;
//     $ar = include $cls_map;
//     Utils::saveCachedClass($ar);
// }

// create_index();

class LoadFile_Bench
{

    public function bench_util_load_cache()
    {
        $ar = Utils::loadCachedClass();
    }
    public function bench_require()
    {
        global $cls_map;
        $ar = include $cls_map;
    }
}
