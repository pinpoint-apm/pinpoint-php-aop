<?php

/******************************************************************************
 * Copyright 2024 NAVER Corp.                                                 *
 *                                                                            *
 * Licensed under the Apache License, Version 2.0 (the "License");            *
 * you may not use this file except in compliance with the License.           *
 * You may obtain a copy of the License at                                    *
 *                                                                            *
 *     http://www.apache.org/licenses/LICENSE-2.0                             *
 *                                                                            *
 * Unless required by applicable law or agreed to in writing, software        *
 * distributed under the License is distributed on an "AS IS" BASIS,          *
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.   *
 * See the License for the specific language governing permissions and        *
 * limitations under the License.                                             *
 ******************************************************************************/
namespace Pinpoint\Plugins\SysV2\_phpredis;

use function Pinpoint\Plugins\SysV2\joinableToString;
use function Pinpoint\Plugins\{
    pinpoint_join_cut,
    pinpoint_start_trace,
    pinpoint_add_clue,
    pinpoint_add_clues,
    pinpoint_end_trace
};

use Redis;

function format_host(Redis $redis): string
{
    $host = $redis->getHost();
    $port = $redis->getPort();
    $db = $redis->getDbNum();

    return "redis(host=$host,port=$port,db=$db)";
}

function make_redis_style_arg_plugins($joinable)
{
    $interceptor_name = joinableToString($joinable);

    $on_before = function (...$args) use ($interceptor_name) {
        $redis = pinpoint_get_this();
        $dst = "localhost:6379";
        if ($redis instanceof Redis) {
            $dst = format_host($redis);
        }
        $key = $args[0];
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, $interceptor_name);
        pinpoint_add_clue(PP_SERVER_TYPE, PP_REDIS);
        pinpoint_add_clue(PP_DESTINATION, $dst);
        pinpoint_add_clues(PP_PHP_ARGS, "$key");
    };

    $on_end = function ($ret) {
        pinpoint_end_trace();
    };

    $on_exception = function ($exp) {
    };

    return [$joinable, $on_before, $on_end, $on_exception];
}

function make_redis_style_plugins($joinable)
{
    $interceptor_name = joinableToString($joinable);

    $on_before = function (...$args) use ($interceptor_name) {
        $redis = pinpoint_get_this();
        $dst = "localhost:6379";
        if ($redis instanceof Redis) {
            $dst = format_host($redis);
        }
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, $interceptor_name);
        pinpoint_add_clue(PP_SERVER_TYPE, PP_REDIS);
        pinpoint_add_clue(PP_DESTINATION, $dst);
    };

    $on_end = function ($ret) {
        pinpoint_end_trace();
    };

    $on_exception = function ($exp) {
    };

    return [$joinable, $on_before, $on_end, $on_exception];
}


$points = [
    make_redis_style_arg_plugins(["Redis", "info"]),
    make_redis_style_arg_plugins(["Redis", "get"]),
    make_redis_style_arg_plugins(["Redis", "set"]),
    make_redis_style_arg_plugins(["Redis", "setNx"]),
    make_redis_style_arg_plugins(["Redis", "append"]),
    make_redis_style_arg_plugins(["Redis", "getRange"]),
    make_redis_style_arg_plugins(["Redis", "setRange"]),
    make_redis_style_arg_plugins(["Redis", "strlen"]),
    make_redis_style_arg_plugins(["Redis", "getBit"]),
    make_redis_style_arg_plugins(["Redis", "setBit"]),

    make_redis_style_plugins(["Redis", "mSet"]),
    make_redis_style_plugins(["Redis", "mSetNx"]),
    make_redis_style_plugins(["Redis", "exists"]),
    make_redis_style_plugins(["Redis", "exec"]),
];

foreach ($points as $point) {
    pinpoint_join_cut(
        $point[0],
        $point[1],
        $point[2],
        $point[3]
    );
}

// author: eeliu