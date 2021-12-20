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
namespace Pinpoint\Plugins\Sys\mysqli;


use Pinpoint\Plugins\Common\PinTrace;

class MysqliPreparePlugin extends PinTrace
{
    function onBefore()
    {
        $myqli = $this->who;
        pinpoint_add_clue(PP_SERVER_TYPE,PP_MYSQL);
        pinpoint_add_clue(PP_SQL_FORMAT, $this->args[0]);
        pinpoint_add_clue(PP_DESTINATION,$myqli->host_info);
    }

    function onEnd(&$ret)
    {
        $origin = $ret;
        $ret = new ProfilerMysqli_Stmt($origin);
    }
}