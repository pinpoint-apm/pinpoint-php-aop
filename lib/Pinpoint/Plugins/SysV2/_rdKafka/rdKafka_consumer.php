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

use function Pinpoint\Plugins\{
    pinpoint_mark_as_error,
    pinpoint_create_transaction_id,
    pinpoint_create_span_id,
    pinpoint_get_context,
    pinpoint_join_cut,
    pinpoint_start_trace,
    pinpoint_add_clue,
    pinpoint_add_clues,
    pinpoint_set_async_ctx,
    pinpoint_end_trace,
    pinpoint_set_context,
    pinpoint_get_trace_depth
};

use Pinpoint\Common\Logger;

// RdKafka\KafkaConsumer::consume
// https://arnaud.le-blanc.net/php-rdkafka-doc/phpdoc/class.rdkafka-kafkaconsumer.html

$interceptor = "RdKafka\KafkaConsumer::consume";

$rdKafka_consumer_on_before = function ($timeout) use ($interceptor) {
    Logger::Inst()->debug("call rdKafka_consumer_on_before");
    $depth = pinpoint_get_trace_depth();
    switch ($depth) {
        case 0:
            pinpoint_end_trace();
            break;
        case -1:
            break;
        default: // >0
            pinpoint_start_trace();
            pinpoint_add_clue(PP_INTERCEPTOR_NAME, $interceptor);
            pinpoint_add_clue(PP_SERVER_TYPE, PP_KAFKA_STREAMS);
            $broker_list_value = pinpoint_get_context(KAFKA_BROKER_LIST);
            if ($broker_list_value) {
                pinpoint_add_clue(PP_DESTINATION, $broker_list_value);
            }
            break;
    }

};

$rdKafka_consumer_on_end = function ($message) use ($interceptor) {
    Logger::Inst()->debug("call rdKafka_consumer_on_end");

    $depth = pinpoint_get_trace_depth();
    switch ($depth) {
        case -1:
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    // start new trace    
                    pinpoint_start_trace();
                    pinpoint_add_clue(PP_SERVER_TYPE, PP_PHP);
                    pinpoint_add_clue(PP_INTERCEPTOR_NAME, $interceptor);
                    pinpoint_add_clue(PP_REQ_URI, "abc");
                    pinpoint_add_clue(PP_REQ_CLIENT, "localhost");
                    pinpoint_add_clue(PP_REQ_SERVER, "localhost");

                    $headers = $message->headers;
                    if (array_key_exists(PP_KAFKA_HEADER_ASYNC_CALL_ID, $headers) && array_key_exists(PP_KAFKA_HEADER_SEQUENCE_ID, $headers)) {
                        $async_id = (int) $headers[PP_KAFKA_HEADER_ASYNC_CALL_ID];
                        $sequence_id = (int) $headers[PP_KAFKA_HEADER_SEQUENCE_ID];
                        pinpoint_set_async_ctx($async_id, $sequence_id);
                    }
                    $tid = pinpoint_create_transaction_id();
                    if (isset($headers[PP_KAFKA_HEADER_TRANSACTION_ID])) {
                        $tid = $headers[PP_KAFKA_HEADER_TRANSACTION_ID];
                    }
                    pinpoint_add_clue(PP_TRANSACTION_ID, $tid);
                    pinpoint_set_context(PP_SPAN_ID, $tid);

                    $sid = pinpoint_create_span_id();
                    if (isset($headers[PP_KAFKA_HEADER_SPAN_ID])) {
                        $sid = $headers[PP_KAFKA_HEADER_SPAN_ID];
                    }
                    pinpoint_add_clue(PP_SPAN_ID, $sid);
                    pinpoint_set_context(PP_SPAN_ID, "$sid");

                    if (isset($headers[PP_KAFKA_HEADER_APP_ID])) {
                        pinpoint_add_clue(PP_APP_ID, $headers[PP_KAFKA_HEADER_APP_ID]);
                    } else {
                        pinpoint_add_clue(PP_APP_ID, APPLICATION_ID);
                    }

                    if (isset($headers[PP_KAFKA_HEADER_APP_NAME])) {
                        pinpoint_add_clue(PP_APP_NAME, $headers[PP_KAFKA_HEADER_APP_NAME]);
                    } else {
                        pinpoint_add_clue(PP_APP_NAME, APPLICATION_NAME);
                    }

                    break;
                default:
                    break;
            }

            break;
        case 0:
            break;
        default:
            // end $interceptor function
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $topic = "unknown";
                    $partition = -1;
                    $topic = $message->topic_name;
                    $partition = $message->partition;
                    pinpoint_add_clues(PP_KAFKA_TOPIC, $topic);
                    pinpoint_add_clues(PP_KAFKA_PARTITION, "$partition");
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    break;
                default:
                    pinpoint_mark_as_error($message->errstr, "", );
                    break;
            }
            pinpoint_end_trace();
            break;
    }

};

$on_exception = function ($exp) {
    Logger::Inst()->debug("call on_exception");
};

pinpoint_join_cut(
    ["RdKafka\KafkaConsumer", "consume"],
    $rdKafka_consumer_on_before,
    $rdKafka_consumer_on_end,
    $on_exception
);


// @author eeliu