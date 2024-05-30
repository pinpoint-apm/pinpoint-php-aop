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
namespace Pinpoint\Plugins\SysV2\_mysqli;

use function Pinpoint\Plugins\{pinpoint_join_cut, pinpoint_start_trace, pinpoint_add_clue, pinpoint_end_trace};
use function Pinpoint\Plugins\SysV2\make_variable_length_list_plugin;
use mysqli;

function make_mysqli_query_plugin()
{
    $on_before = function (string $query, int $result_mode = MYSQLI_STORE_RESULT) {
        $msqli = pinpoint_get_this();
        $db_host = "localhost";
        if ($msqli instanceof mysqli) {
            $db_host = $msqli->host_info;
        }
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, "mysqli::query");
        pinpoint_add_clue(PP_SERVER_TYPE, PP_MYSQL);
        pinpoint_add_clue(PP_SQL_FORMAT, $query);
        pinpoint_add_clue(PP_DESTINATION, $db_host);
    };

    $on_end = function ($ret) {
        pinpoint_end_trace();
    };

    $on_exception = function ($exp) {
    };

    return [['mysqli', 'query'], $on_before, $on_end, $on_exception];
}


function make_mysqli_prepare_plugin()
{
    $on_before = function (string $query) {
        $mysqli = pinpoint_get_this();
        $db_host = "localhost";
        if ($mysqli instanceof mysqli) {
            $db_host = $mysqli->host_info;
        }
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, "mysqli::prepare");
        pinpoint_add_clue(PP_SERVER_TYPE, PP_MYSQL);
        pinpoint_add_clue(PP_SQL_FORMAT, $query);
        pinpoint_add_clue(PP_DESTINATION, $db_host);
    };

    $on_end = function ($ret) {
        pinpoint_end_trace();
    };

    $on_exception = function ($exp) {
    };

    return [['mysqli', 'prepare'], $on_before, $on_end, $on_exception];
}

$points = [
    make_mysqli_prepare_plugin(),
    make_mysqli_query_plugin(),
    make_variable_length_list_plugin(['mysqli_stmt', 'execute']),
    make_variable_length_list_plugin(['mysqli_stmt', 'fetch'])
];

foreach ($points as $point) {
    pinpoint_join_cut($point[0], $point[1], $point[2], $point[3]);
}



// author: eeliu