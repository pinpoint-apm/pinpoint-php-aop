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
namespace Pinpoint\Plugins\autoload\_predis;

use Pinpoint\Plugins\Common\PinTrace;
use Pinpoint\Common\Logger;

use function Pinpoint\Plugins\{
    pinpoint_add_clue,
    pinpoint_add_clues,
};

class pRedisCall extends PinTrace
{
    public function __construct($monitorName, $who, &...$args)
    {
        $func_name = $args[0];
        parent::__construct("Predis::$func_name", $who, ...$args);
    }
    function onBefore()
    {
        pinpoint_add_clue(PP_SERVER_TYPE, PP_REDIS);
        pinpoint_add_clues(PP_PHP_ARGS, json_encode($this->args[1]));
        if ($this->who instanceof \Predis\Client) {
            $parm = $this->who->getConnection()->getParameters();
            $host = $parm->host ?: "localhost";
            $scheme = $parm->scheme ?: "unix";
            $port = $parm->port ?: "6379";
            pinpoint_add_clue(PP_DESTINATION, "$scheme://$host:$port");
        }
    }

    function onEnd(&$ret)
    {

    }

    function onException($e)
    {
        Logger::Inst()->debug(__CLASS__ . "onException '$e'");
    }
}

// author: eeliu
