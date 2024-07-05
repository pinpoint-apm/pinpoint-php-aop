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


namespace Pinpoint\Plugins\autoload\_MongoPlugin;

use Pinpoint\Plugins\Common\PinTrace;
use function Pinpoint\Plugins\{pinpoint_add_clue, pinpoint_add_clues};

class MongoPlugin extends PinTrace
{
    function onBefore()
    {
        if (strpos($this->monitor_name, "Client::__construct")) {
            $url = $this->args[0][0][0];
            pinpoint_add_clue(PP_SERVER_TYPE, PP_MONGODB_EXE_QUERY);
            pinpoint_add_clue(PP_DESTINATION, $url);
            return;
        }
        pinpoint_add_clue(PP_SERVER_TYPE, PP_MONGODB_EXE_QUERY);
        pinpoint_add_clues(PP_DESTINATION, json_encode($this->args[0], JSON_UNESCAPED_SLASHES));
    }

    function onEnd(&$ret)
    { // do nothing
    }

    function onException($e)
    {// do nothing
    }
}