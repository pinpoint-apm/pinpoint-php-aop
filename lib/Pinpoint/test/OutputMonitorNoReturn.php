<?php

namespace Pinpoint\test;

use Pinpoint\Common\AbstractMonitor;

class OutputMonitorNoReturn extends AbstractMonitor
{
    function onBefore()
    {
    }

    function onEnd(&$ret)
    {
    }

    function onException($e)
    {
    }
}
