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
namespace Pinpoint\Plugins\autoload\_GuzzleHttp;

use function Pinpoint\Plugins\{pinpoint_add_clue, pinpoint_add_clues, pinpoint_get_context};
use Pinpoint\Plugins\Common\PinTrace;
use Pinpoint\Plugins\Sys\curl\CurlUtil;

class GuzzlePlugin extends PinTrace
{
    function onBefore()
    {
        var_dump($this->args);
        pinpoint_add_clue(PP_DESTINATION, CurlUtil::getHostFromURL((string) ($this->args[1])));
        pinpoint_add_clues(PP_HTTP_URL, $this->args[1]);
        pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP_REMOTE);

        $pp_headers = [];
        if (is_array($this->args[2]) && array_key_exists('headers', $this->args[2])) {
            $pp_headers = $this->args[2];
        }
        $pp_headers = array_merge($pp_headers, CurlUtil::getPPHeader($this->args[1]));

        $this->args[2]['headers'] = $pp_headers;
    }

    function onEnd(&$ret)
    {
        pinpoint_add_clue(PP_NEXT_SPAN_ID, pinpoint_get_context(PP_NEXT_SPAN_ID));
        pinpoint_add_clues(PP_HTTP_STATUS_CODE, (string) ($ret->getStatusCode()));
    }

    function onException($e)
    {
        pinpoint_add_clue(PP_ADD_EXCEPTION, $e->getMessage());
    }
}