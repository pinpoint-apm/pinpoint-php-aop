<?php

namespace Pinpoint\test;

use Pinpoint\Common\AbstractMonitor;
use Pinpoint\Common\Logger;

class OutputMonitor extends AbstractMonitor
{
    function onBefore()
    {
        Logger::Inst()->debug("onbefore");
    }

    function onEnd(&$ret)
    {
        if ($ret === 1010) {
            $ret = 1011;
        }
        Logger::Inst()->debug("onEnd");
    }

    function onException($e)
    {
        Logger::Inst()->debug("onException");
    }
}
