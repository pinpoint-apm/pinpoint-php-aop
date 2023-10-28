<?php

namespace Pinpoint\test;

use Pinpoint\Common\Monitor;

class OutputMonitor extends Monitor
{
    function onBefore()
    {
    }

    function onEnd(&$ret)
    {
        return 1011;
    }

    function onException($e)
    {
    }
}
