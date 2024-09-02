<?php

/******************************************************************************
 * Copyright 2020 NAVER Corp.                                                 *
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

class LaravelPerRequest extends DefaultRequestPlugin
{
    public function __destruct()
    {
        $route = Request()->route();
        if ($route) {
            pinpoint_set_context(PP_ROUTE_KEY, Request()->route()->uri());
        } else {
            pinpoint_set_context(PP_ROUTE_KEY, "_none_");
        }
        parent::__destruct();
    }
}
//author: eeliu

// Changes
// 2024/9/02
// fix "Request()->route()" can be none