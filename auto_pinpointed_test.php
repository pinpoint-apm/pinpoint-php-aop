<?php

declare(strict_types=1);

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

namespace Pinpoint;

require_once __DIR__ . "/vendor/autoload.php";

use Pinpoint\Common\PinpointDriver;
use Pinpoint\Common\Logger;

use Pinpoint\Plugins\PinpointPerRequestPlugins;
use Pinpoint\Common\UserFrameworkInterface;

class RequestPlugin extends PinpointPerRequestPlugins implements UserFrameworkInterface
{
    public function __construct()
    {
        parent::__construct();
    }
    public function joinedClassSet(): array
    {
        $ar = require_once __DIR__ . "/lib/Pinpoint/Plugins/autoload/__init__.php";

        return $ar;
    }
    public function userFindClass(&$loader): callable
    {
        return [NULL];
    }
}
define('APPLICATION_NAME', 'cd.dev.test.php');
define('APPLICATION_ID', 'cd.dev.ci');
define('PP_REQ_PLUGINS', RequestPlugin::class);


Logger::Inst()->setLoggerLevel(4);
PinpointDriver::getInstance()->cleanCache();
PinpointDriver::getInstance()->start();