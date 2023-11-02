<?php

declare(strict_types=1);
/**
 * Copyright 2020-present NAVER Corp.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Pinpoint\Common;

use Monolog\Logger as mlogger;
use Monolog\Handler\StreamHandler;

class PerRequestDefault implements UserFrameworkInterface
{
    public function __construct()
    {
        $log = new mlogger('pp');
        $log->pushHandler(new StreamHandler('php://stdout', mlogger::DEBUG));
        Logger::Inst()->setLogger($log);
    }
    public function joinedClassSet(): array
    {
        return [];
    }

    public function findClass($name): string
    {
        return "";
    }

    public function userFindClass(&$loader): callable
    {
        return [$this, 'findClass'];
    }
}
