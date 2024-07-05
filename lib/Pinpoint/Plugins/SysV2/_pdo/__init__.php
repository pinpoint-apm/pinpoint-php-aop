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

require_once __DIR__ . "/common.php";

if (!extension_loaded('pdo')) {
    return;
}

if (version_compare(phpversion(), '8.2.0', '>=')) {
    require_once __DIR__ . "/pdo8.php";
} else {
    require_once __DIR__ . "/pdo7.php";
}

// author: eeliu