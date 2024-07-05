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
namespace Pinpoint\Plugins\SysV2\_memcached;

use function Pinpoint\Plugins\SysV2\joinableToString;
use function Pinpoint\Plugins\{
    pinpoint_join_cut,
    pinpoint_start_trace,
    pinpoint_add_clue,
    pinpoint_add_clues,
    pinpoint_end_trace
};

use Memcached;

function format_host(Memcached $Memcached): string
{
    $servers = $Memcached->getServerList();
    $ret = "";
    foreach ($servers as $ser) {
        $host = $ser['host'];
        $port = $ser['port'];
        $weight = $ser['weight'];
        $ret .= "memcached(host=$host,port=$port,weight=$weight)";
    }
    return $ret;
}

function make_memcached_style_arg_plugins($joinable)
{
    $interceptor_name = joinableToString($joinable);

    $on_before = function (...$args) use ($interceptor_name) {
        $mc = pinpoint_get_this();
        $dst = "localhost:11211";
        if ($mc instanceof Memcached) {
            $dst = format_host($mc);
        }
        $key = $args[0];
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, $interceptor_name);
        pinpoint_add_clue(PP_SERVER_TYPE, PP_MEMCACHED);
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

function make_memcached_style_plugins($joinable)
{
    $interceptor_name = joinableToString($joinable);

    $on_before = function (...$args) use ($interceptor_name) {
        $mc = pinpoint_get_this();
        $dst = "localhost:11211";
        if ($mc instanceof Memcached) {
            $dst = format_host($mc);
        }
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, $interceptor_name);
        pinpoint_add_clue(PP_SERVER_TYPE, PP_MEMCACHED);
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
    make_memcached_style_arg_plugins(["Memcached", "add"]),
    make_memcached_style_arg_plugins(["Memcached", "get"]),
    make_memcached_style_arg_plugins(["Memcached", "set"]),
    make_memcached_style_arg_plugins(["Memcached", "append"]),
    make_memcached_style_arg_plugins(["Memcached", "appendByKey"]),
    make_memcached_style_arg_plugins(["Memcached", "decrement"]),
    make_memcached_style_arg_plugins(["Memcached", "decrementByKey"]),
    make_memcached_style_arg_plugins(["Memcached", "deleteByKey"]),
    make_memcached_style_arg_plugins(["Memcached", "getByKey"]),
    make_memcached_style_arg_plugins(["Memcached", "incrementByKey"]),
    make_memcached_style_arg_plugins(["Memcached", "increment"]),

    make_memcached_style_plugins(["Memcached", "deleteMulti"]),
    make_memcached_style_plugins(["Memcached", "deleteMultiByKey"]),
    make_memcached_style_plugins(["Memcached", "getAllKeys"]),
    make_memcached_style_plugins(["Memcached", "getMultiByKey"]),
    make_memcached_style_plugins(["Memcached", "setMulti"]),
    make_memcached_style_plugins(["Memcached", "getMulti"]),
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