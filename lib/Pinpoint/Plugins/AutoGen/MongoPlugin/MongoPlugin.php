<?php
#-------------------------------------------------------------------------------
# Copyright 2020 NAVER Corp
#
# Licensed under the Apache License, Version 2.0 (the "License"); you may not
# use this file except in compliance with the License.  You may obtain a copy
# of the License at
#
#   http://www.apache.org/licenses/LICENSE-2.0
#
# Unless required by applicable law or agreed to in writing, software
# distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
# WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.  See the
# License for the specific language governing permissions and limitations under
# the License.
#-------------------------------------------------------------------------------


namespace Pinpoint\Plugins;
use Pinpoint\Plugins\Common\PinTrace;

class MongoPlugin extends PinTrace
{

    /**
     * @hook:MongoDB\Client::__construct
     * @hook:MongoDB\Collection::insertOne
     * @hook:MongoDB\Collection::updateOne
     * @hook:MongoDB\Collection::deleteMany
     * @hook:MongoDB\Collection::find
     */
    function onBefore()
    {
        if(strpos($this->apId, "Client::__construct")) {
            $url=$this->args[0];
            pinpoint_add_clue(SERVER_TYPE,MONGODB_EXE_QUERY);
            pinpoint_add_clue(DESTINATION, $url);
            return;
        }
        pinpoint_add_clue(SERVER_TYPE,MONGODB_EXE_QUERY);
        pinpoint_add_clues(PHP_ARGS, print_r($this->args[0],true));
    }

    function onEnd(&$ret)
    { // do nothing
    }

    function onException($e)
    {// do nothing
    }
}