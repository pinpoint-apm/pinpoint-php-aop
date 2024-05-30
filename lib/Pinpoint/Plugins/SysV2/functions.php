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
namespace Pinpoint\Plugins\SysV2;

use Pinpoint\Common\Logger;
use function Pinpoint\Plugins\{pinpoint_get_context, pinpoint_set_context};
use Pinpoint\Plugins\Common\TraceHelper;

function make_variable_length_list_plugin(array $joinable)
{
    $funcPlugin = new FuncPlugin($joinable);

    $on_before = function ($_1 = null) use ($funcPlugin) {
        Logger::Inst()->debug("call $funcPlugin->name on_before");
        $funcPlugin->onBefore();
    };

    $on_end = function ($ret) use ($funcPlugin) {
        Logger::Inst()->debug("call $funcPlugin->name on_end");
        $funcPlugin->onEnd();
    };

    $on_exception = function ($exp) use ($funcPlugin) {
        Logger::Inst()->debug("call $funcPlugin->name on_exception");
        $funcPlugin->onException($exp);
    };

    return [$joinable, $on_before, $on_end, $on_exception];
}

function make_3_plugin(array $joinable)
{
    $funcPlugin = new FuncPlugin($joinable);

    $on_before = function () use ($funcPlugin) {
        Logger::Inst()->debug("call $funcPlugin->name on_before");
        $funcPlugin->onBefore();
    };

    $on_end = function ($ret) use ($funcPlugin) {
        Logger::Inst()->debug("call $funcPlugin->name on_end");
        $funcPlugin->onEnd();
    };

    $on_exception = function ($exp) use ($funcPlugin) {
        Logger::Inst()->debug("call $funcPlugin->name on_exception");
        $funcPlugin->onException($exp);
    };

    return [$joinable, $on_before, $on_end, $on_exception];
}

function joinableToString(array $joinable): string
{
    if (count($joinable) == 1) {
        return $joinable[0];
    } else {
        return "$joinable[0]:$joinable[1]";
    }
}

function genUrlNextSpan($url)
{
    if (pinpoint_get_context('Pinpoint-Sampled') == PP_NOT_SAMPLED) {
        return ["Pinpoint-Sampled:s0"];
    }

    $nextSid = TraceHelper::generateSpanID();
    $header = [
        'Pinpoint-Sampled:s1',
        'Pinpoint-Flags:0',
        'Pinpoint-Papptype:1500',
        'Pinpoint-Pappname:' . APPLICATION_NAME,
        'Pinpoint-Host:' . getHostFromURL($url),
        'Pinpoint-Traceid:' . pinpoint_get_context(PP_TRANSCATION_ID),
        'Pinpoint-Pspanid:' . pinpoint_get_context(PP_SPAN_ID),
        "Pinpoint-Spanid: $nextSid"
    ];
    pinpoint_set_context(PP_NEXT_SPAN_ID, "$nextSid");
    return $header;
}

// for GuzzleHttp header
function getPPHeader($url)
{
    if (pinpoint_get_context('Pinpoint-Sampled') == PP_NOT_SAMPLED) {
        return ["Pinpoint-Sampled" => "s0"];
    }

    $nsid = TraceHelper::generateSpanID();
    $header = [
        'Pinpoint-Sampled' => 's1',
        'Pinpoint-Flags' => '0',
        'Pinpoint-Papptype' => '1500',
        'Pinpoint-Pappname' => APPLICATION_NAME,
        'Pinpoint-Host' => getHostFromURL($url),
        'Pinpoint-Traceid' => pinpoint_get_context(PP_TRANSCATION_ID),
        'Pinpoint-Pspanid' => pinpoint_get_context(PP_SPAN_ID),
        'Pinpoint-Spanid' => $nsid
    ];
    pinpoint_set_context(PP_NEXT_SPAN_ID, (string) $nsid);
    return $header;
}

/**
 *
 * url is very funny
 * example.com
 * www.example.com:8000
 * www.example.com
 * http://www.example.com
 *  total must be accept
 *
 * @param string $url
 * @return string
 */
function getHostFromURL(string $url)
{
    $urlAr = parse_url($url);
    $retUrl = '';

    if (isset($urlAr['host'])) // got the host and return
    {
        $retUrl .= $urlAr['host'];
    }

    if (isset($urlAr['port'])) // an optional setting
    {
        $retUrl .= ":" . $urlAr['port'];
    }

    return $retUrl;
}


// author: eeliu