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

namespace Pinpoint\Plugins;

if (!extension_loaded('pinpoint_php')) {
    throw new \Exception("pinpoint_php module not load. Please check this guide: https://github.com/pinpoint-apm/pinpoint-c-agent/blob/dev/DOC/PHP/Readme.md#installation or Mailto: dl_cd_pinpoint@navercorp.com ");
}

require_once __DIR__ . "/SysV2/functions.php";

define("PINPOINT_ROOT_LOC", 1);

/**
 * Summary of Pinpoint\Plugins\pinpoint_start_trace
 * @param mixed $id
 * @return int new trace if from $id
 */
function pinpoint_start_trace($id = -1): int
{
    return _pinpoint_start_trace($id);
}

/**
 * Summary of Pinpoint\Plugins\pinpoint_end_trace
 * @param mixed $id
 * @return int parent trace
 */
function pinpoint_end_trace($id = -1): int
{
    return _pinpoint_end_trace($id);
}

/**
 * insert key,value into current span/spanevent
 * @param string $key
 * @param string $value
 */
function pinpoint_add_clue(string $key, string $value, $id = -1, $loc = 0): void
{
    _pinpoint_add_clue($key, $value, $id, $loc);
}

/**
 * insert key,value into current trace context
 * @param string $key
 * @param string $value
 */
function pinpoint_set_context(string $key, string $value, $id = -1, $loc = 0): void
{
    _pinpoint_set_context($key, $value, $id);
}

/**
 * get key-value from current trace
 * @param string $key
 * @return string value
 */
function pinpoint_get_context(string $key, $id = -1)
{
    return _pinpoint_get_context($key, $id);
}

/**
 * insert key,value into current span/span event annotation
 * @param int $key
 * @param string $value
 */
function pinpoint_add_clues(string $key, string $value, $id = -1, $loc = 0): void
{
    _pinpoint_add_clues($key, $value, $id, $loc);
}

/**
 * Summary of Pinpoint\Plugins\pinpoint_unique_id
 * @return int return a process based id
 */
function pinpoint_unique_id(): int
{
    return _pinpoint_unique_id();
}

/**
 * if $timestamp is given, use $timestamp. Or use time()
 * @param int $timestamp
 */
function pinpoint_tracelimit(int $timestamp = 0)
{
    return _pinpoint_trace_limit($timestamp);
}

/**
 * Summary of Pinpoint\Plugins\pinpoint_drop_trace
 * drop current trace tree by $id
 * trace id can be any trace node of trace tree.
 * @param mixed $id
 * @return mixed
 */
function pinpoint_drop_trace($id = -1)
{
    return _pinpoint_drop_trace($id);
}

/**
 * Summary of Pinpoint\Plugins\pinpoint_start_time
 * @return int start_time, use to generate TransactionID
 */
function pinpoint_start_time(): int
{
    return _pinpoint_start_time();
}

/**
 * @description: mark current span ($id) as error
 * @param {string} $msg
 * @param {string} $filename
 * @param {int} $lineno
 * @param {*} $id =-1 that means use the thread-local id
 */
function pinpoint_mark_as_error(string $msg, string $filename, int $lineno = 0, $id = -1): void
{
    _pinpoint_mark_as_error($msg, $filename, $lineno, $id);
}

/**
 * Summary of pinpoint_join_cut
 * onBefore:  function onBefore($a,$b,)
 *  $a,$b, should be the same as joinable function/method
 * onEnd: function onEnd($ret)
 *  $ret is the same of $joinable function return value
 * onException: function onException($e): 
 *  $e: should be the exception
 * @param array $joinable ["PDO","exec"] , ["mysql_init"]
 * @param callable $onBefore
 * @param callable $onEnd
 * @param callable $onException
 * @return void
 */
function pinpoint_join_cut(array $joinable, callable $onBefore, callable $onEnd, callable $onException): bool
{
    // TODO check parameters in joinable
    return _pinpoint_join_cut($joinable, $onBefore, $onEnd, $onException);
}

//author @eeliu