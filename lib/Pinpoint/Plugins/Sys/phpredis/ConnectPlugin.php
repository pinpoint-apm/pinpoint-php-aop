<?php
/*
 * User: eeliu
 * Date: 11/5/20
 * Time: 4:18 PM
 */

namespace Pinpoint\Plugins\Sys\phpredis;


use Pinpoint\Plugins\Common\PinTrace;

class ConnectPlugin extends  PinTrace
{

    function onBefore()
    {
        $host = $this->args[0];
        $port = $this->args[1];
        pinpoint_add_clue(PP_SERVER_TYPE,PP_REDIS);
        pinpoint_add_clue(PP_DESTINATION,"$host:$port");
    }

    function onEnd(&$ret)
    {
        // TODO: Implement onEnd() method.
    }
}