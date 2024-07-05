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
namespace Pinpoint\Plugins\SysV2;

use function Pinpoint\Plugins\{
    pinpoint_start_trace,
    pinpoint_add_clues,
    pinpoint_add_clue,
    pinpoint_end_trace
};

class FuncPlugin
{
    public $name;
    public $joinable = [];
    public function __construct(array $joinable)
    {
        $this->name = joinableToString($joinable);
        $this->joinable = $joinable;
    }
    public function onBefore(string $arg = "")
    {
        pinpoint_start_trace();
        pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP_METHOD);
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, $this->name);
        if ($arg) {
            pinpoint_add_clues(PP_PHP_ARGS, $arg);
        }
    }
    public function onEnd(string $value = "")
    {
        if ($value) {
            pinpoint_add_clues(PP_PHP_RETURN, $value);
        }
        pinpoint_end_trace();
    }

    public function onException(\Exception $e)
    {
        pinpoint_add_clue(PP_ADD_EXCEPTION, "$e");
    }
}


// author: eeliu