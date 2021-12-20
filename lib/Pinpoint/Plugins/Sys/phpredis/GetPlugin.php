<?php
/*
 * User: eeliu
 * Date: 11/5/20
 * Time: 4:32 PM
 */

namespace Pinpoint\Plugins\Sys\phpredis;


use Pinpoint\Plugins\Common\PinTrace;

class GetPlugin extends PinTrace
{

    function onBefore()
    {
        $key = $this->args[0];
        pinpoint_add_clue(PP_SERVER_TYPE,PP_REDIS);
        pinpoint_add_clues(PP_PHP_ARGS,"$key");
    }

    function onEnd(&$ret)
    {
        // TODO: Implement onEnd() method.
    }
}