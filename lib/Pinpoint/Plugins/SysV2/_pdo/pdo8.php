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
// namespace Pinpoint\Plugins\SysV2\_pdo;

// use function Pinpoint\Plugins\{pinpoint_join_cut};

// pinpoint_join_cut(
//     ["PDO", "__construct"],
//     function ($dsn, $username = null, $password = null, $options = null) {
//         echo "on_before: $dsn \n";
//         $pdo = pinpoint_get_this();
//         if ($pdo instanceof PDO) {
//             $pdo->dsn = $dsn;
//             echo "attached dsn \n";
//         }
//     },
//     function ($ret) {
//         echo "on_end \n";
//     },
//     function ($e) {
//         echo "on_exception \n";
//     }
// );

// author: eeliu