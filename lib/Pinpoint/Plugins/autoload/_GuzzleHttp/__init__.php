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
namespace Pinpoint\Plugins\autoload\_GuzzleHttp;


use Pinpoint\Common\AspectClassHandle;

$cls = [];

$classHandler = new AspectClassHandle(\GuzzleHttp\Client::class);
if (extension_loaded('curl')) {
    $classHandler->addJoinPoint('request', \Pinpoint\Plugins\Common\CommonPlugin::class);
} else {
    $classHandler->addJoinPoint('request', GuzzlePlugin::class);
}

$cls[] = $classHandler;

// $classHandler = new AspectClassHandle(\GuzzleHttp\Psr7\Request::class);
// $classHandler->addJoinPoint('__construct', GuzzlePlugin::class);
// $cls[] = $classHandler;


return $cls;

// author: eeliu