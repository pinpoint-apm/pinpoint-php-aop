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



/**pinpoint_start_trace
 * User: eeliu
 * Date: 1/4/19
 * Time: 3:23 PM
 */

namespace Pinpoint\Plugins\Common;

use function Pinpoint\Plugins\{pinpoint_start_trace, pinpoint_add_clue, pinpoint_end_trace};
use Pinpoint\Common\Logger;

require_once __DIR__ . "/defines.php";


class PinTrace extends Trace
{

    public function __construct($monitorName, $who, &...$args)
    {
        parent::__construct($monitorName, $who, ...$args);
        Logger::Inst()->debug("[pp] call pinpoint_start_trace $monitorName");
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, $monitorName);
    }

    public function __destruct()
    {
        pinpoint_end_trace();
        Logger::Inst()->debug("[pp] call pinpoint_end_trace $this->monitor_name");
    }

    public function onException($e)
    {
        Logger::Inst()->debug("[pp] call onException $this->monitor_name");
        pinpoint_add_clue(PP_ADD_EXCEPTION, $e->getMessage());
    }
}
