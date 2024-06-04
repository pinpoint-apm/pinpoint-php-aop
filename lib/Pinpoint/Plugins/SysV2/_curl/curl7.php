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
namespace Pinpoint\Plugins\SysV2\_curl;

use function Pinpoint\Plugins\{
    pinpoint_join_cut,
    pinpoint_start_trace,
    pinpoint_add_clue,
    pinpoint_end_trace,
    pinpoint_get_context,
    pinpoint_add_clues
};
use function Pinpoint\Plugins\SysV2\{getHostFromURL, genUrlNextSpan};

$ch_res = [];

pinpoint_join_cut(
    ["curl_setopt"],
    function ($ch, $option, $value) use (&$ch_res) {
        if ($option == CURLOPT_HTTPHEADER && is_array($value)) {
            $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
            $nextSpan = new NextSpan($url);
            $ch_res[(int) $ch] = $nextSpan;
            $value = array_merge($value, $nextSpan->genNextSpan());
            return [$ch, $option, $value];
        }
    },
    function ($ret) {
    },
    function ($e) {
    }
);

pinpoint_join_cut(
    ["curl_exec"],
    function ($ch) use (&$ch_res) {
        $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        if (!$ch_res[(int) $ch]) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, genUrlNextSpan($url));
        }
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, "curl_exec");
        pinpoint_add_clue(PP_DESTINATION, getHostFromURL($url));
        pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP_REMOTE);
        pinpoint_add_clue(PP_NEXT_SPAN_ID, pinpoint_get_context(PP_NEXT_SPAN_ID));
        pinpoint_add_clues(PP_HTTP_URL, $url);
        pinpoint_add_clues(PP_PHP_ARGS, "$url");
    },
    function ($ret) {
        $ch = pinpoint_get_caller_arg(0);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($code >= 400) {
            pinpoint_add_clue(PP_ADD_EXCEPTION, "http_code: $code");
        }
        pinpoint_add_clues(PP_HTTP_STATUS_CODE, $code);
        pinpoint_end_trace();
    },
    function ($e) {
    }
);

pinpoint_join_cut(
    ["curl_close"],
    function ($ch) use (&$ch_res) {
        unset($ch_res[(int) $ch]);
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME, "curl_close");
        pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP_METHOD);
    },
    function ($ret) {
        pinpoint_end_trace();
    },
    function ($e) {
    }
);

// author: eeliu