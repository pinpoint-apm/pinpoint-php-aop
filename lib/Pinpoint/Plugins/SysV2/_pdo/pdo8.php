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
namespace Pinpoint\Plugins\SysV2\_pdo;

$weakMap = new \WeakMap();

use function Pinpoint\Plugins\{pinpoint_join_cut, pinpoint_start_trace, pinpoint_add_clue, pinpoint_end_trace};
use function Pinpoint\Plugins\SysV2\make_variable_length_list_plugin;
use PDO;

pinpoint_join_cut(
    ["PDO", "__construct"],
    function ($dsn, $username = null, $password = null, $options = null) use ($weakMap) {
        $pdo = pinpoint_get_this();
        if ($pdo instanceof PDO) {
            $weakMap[$pdo] = $dsn;
        }
    },
    function ($ret) {
    },
    function ($e) {
    }
);

pinpoint_join_cut(
    ["PDO", "query"],
    function ($query) use ($weakMap) {
        $pdo = pinpoint_get_this();
        $db_host = "localhost";
        if ($pdo instanceof PDO) {
            $db_host = $weakMap[$pdo];
        }
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, "PDO::query");
        pinpoint_add_clue(PP_SERVER_TYPE, PP_MYSQL);
        pinpoint_add_clue(PP_SQL_FORMAT, $query);
        pinpoint_add_clue(PP_DESTINATION, $db_host);
    },
    function ($ret) {
        pinpoint_end_trace();
    },
    function ($e) {
    }
);

pinpoint_join_cut(
    ["PDO", "prepare"],
    function (string $query, array $options = []) use ($weakMap) {
        $pdo = pinpoint_get_this();
        $db_host = "localhost";
        if ($pdo instanceof PDO) {
            $db_host = $weakMap[$pdo];
        }
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, "PDO::prepare");
        pinpoint_add_clue(PP_SERVER_TYPE, PP_MYSQL);
        pinpoint_add_clue(PP_SQL_FORMAT, $query);
        pinpoint_add_clue(PP_DESTINATION, $db_host);
    },
    function ($ret) {
        pinpoint_end_trace();
    },
    function ($e) {
    }
);

$points = [
    make_variable_length_list_plugin(["PDOStatement", "execute"]),
    make_variable_length_list_plugin(["PDOStatement", "fetch"]),
    make_variable_length_list_plugin(["PDOStatement", "fetchAll"]),
];

foreach ($points as $point) {
    pinpoint_join_cut($point[0], $point[1], $point[2], $point[3]);
}

// author: eeliu