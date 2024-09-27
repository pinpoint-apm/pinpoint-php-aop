<?php
namespace Pinpoint\Plugins\SysV2\_rdKafka;

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

use function Pinpoint\Plugins\pinpoint_get_context;
use function Pinpoint\Plugins\pinpoint_get_sequence_id;
use function Pinpoint\Plugins\{
    pinpoint_join_cut,
    pinpoint_start_trace,
    pinpoint_add_clue,
    pinpoint_add_clues,
    pinpoint_set_context,
    pinpoint_end_trace
};
use Pinpoint\Common\Logger;

const KAFKA_BROKER_LIST = "__kafka_broker_list__";

$config_set_on_before = function ($name, $value) {
    if ($name == 'metadata.broker.list') {
        // store value into pinpoint context
        pinpoint_set_context(KAFKA_BROKER_LIST, $value);
    }
};

$RdKafka_add_brokers_on_before = function ($blocker_list) {
    if (!empty($blocker_list)) {
        pinpoint_set_context(KAFKA_BROKER_LIST, $blocker_list);
    }
};

function get_broker_list_plugins($joinable, $on_before)
{
    $on_end = function ($ret) {
    };
    $on_exception = function ($exp) {
    };
    return [$joinable, $on_before, $on_end, $on_exception];
}

$producev_on_before = function ($partition, $msgflags, $payload, $key = NULL, $headers = [], $timestamp_ms = NULL, $opaque = NULL) {
    Logger::Inst()->debug("call on_before");
    pinpoint_start_trace();
    pinpoint_add_clue(PP_INTERCEPTOR_NAME, "RdKafka\ProducerTopic::producev");
    pinpoint_add_clue(PP_SERVER_TYPE, PP_KAFKA);
    $topic_pro = pinpoint_get_this();
    $topic = "unknown";
    if ($topic_pro instanceof \RdKafka\ProducerTopic) {
        $topic = $topic_pro->getName();
    }
    pinpoint_add_clues(PP_KAFKA_TOPIC, $topic);
    pinpoint_add_clues(PP_KAFKA_PARTITION, "$partition");
    $broker_list_value = pinpoint_get_context(KAFKA_BROKER_LIST);
    if ($broker_list_value) {
        pinpoint_add_clue(PP_DESTINATION, $broker_list_value);
    }
    $async_id = mt_rand();
    pinpoint_add_clue(PP_ASYNC_CALL_ID, $async_id);
    $sequence_id = pinpoint_get_sequence_id();

    $headers[PP_KAFKA_HEADER_ASYNC_CALL_ID] = $async_id;
    $headers[PP_KAFKA_HEADER_SEQUENCE_ID] = $sequence_id;

    $headers[PP_KAFKA_HEADER_TRANSACTION_ID] = pinpoint_get_context(PP_TRANSACTION_ID);
    $headers[PP_KAFKA_HEADER_SPAN_ID] = pinpoint_get_context(PP_SPAN_ID);
    $headers[PP_KAFKA_HEADER_APP_ID] = APPLICATION_ID;
    $headers[PP_KAFKA_HEADER_APP_NAME] = APPLICATION_NAME;


    return [$partition, $msgflags, $payload, $key, $headers, $timestamp_ms, $opaque];
};

$produce_on_before = function ($partition, $msgflags, $payload = NULL, $key = NULL, $headers = NULL, $opaque = NULL) {
    Logger::Inst()->debug("call on_before");
    pinpoint_start_trace();
    pinpoint_add_clue(PP_INTERCEPTOR_NAME, "RdKafka\ProducerTopic::produce");
    pinpoint_add_clue(PP_SERVER_TYPE, PP_KAFKA);
    $topic_pro = pinpoint_get_this();
    $topic = "unknown";
    if ($topic_pro instanceof \RdKafka\ProducerTopic) {
        $topic = $topic_pro->getName();
    }
    pinpoint_add_clues(PP_KAFKA_TOPIC, $topic);
    pinpoint_add_clues(PP_KAFKA_PARTITION, "$partition");
    $broker_list_value = pinpoint_get_context(KAFKA_BROKER_LIST);
    if ($broker_list_value) {
        pinpoint_add_clue(PP_DESTINATION, $broker_list_value);
    }
};


$on_end = function ($ret) {
    Logger::Inst()->debug("call on_end");
    pinpoint_end_trace();
};

$on_exception = function ($exp) {
    Logger::Inst()->debug("call on_exception");
};

// https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka-producertopic.producev.html
pinpoint_join_cut(
    ["RdKafka\ProducerTopic", "producev"],
    $producev_on_before,
    $on_end,
    $on_exception
);

pinpoint_join_cut(
    ["RdKafka\ProducerTopic", "produce"],
    $produce_on_before,
    $on_end,
    $on_exception
);

$points = [
    get_broker_list_plugins(["RdKafka\Conf", "set"], $config_set_on_before), // https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka-conf.set.html
    get_broker_list_plugins(["RdKafka", "addBrokers"], $RdKafka_add_brokers_on_before) //https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/rdkafka.addbrokers.html
];

foreach ($points as $point) {
    pinpoint_join_cut(
        $point[0],
        $point[1],
        $point[2],
        $point[3]
    );
}
// @author eeliu