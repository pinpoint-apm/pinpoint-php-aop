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
/*
 * User: eeliu
 * Date: 12/20/21
 * Time: 5:12 PM
 */

namespace Pinpoint\Plugins\Common;

use Pinpoint\Common\AbstractMonitor;

class Trace extends AbstractMonitor
{
    public function __construct($apId, $who, &...$args)
    {
        parent::__construct($apId, $who, $args);
    }

    public function __destruct()
    {
    }

    function onBefore()
    {
    }

    function onEnd(&$ret)
    {
    }

    public function onException($e)
    {
    }
}
