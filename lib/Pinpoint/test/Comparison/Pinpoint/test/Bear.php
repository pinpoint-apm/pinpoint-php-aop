<?php

namespace Pinpoint\test;

use Pinpoint\test\ProxyBear;
class Bear extends ProxyBear
{
    public function output(string $_1, int $_2, array &$_3)
    {
        $_pinpoint_output_var = new \Pinpoint\test\OutputMonitor(__METHOD__, $this, $_1, $_2, $_3);
        $_pinpoint_output_ret = null;
        try {
            $_pinpoint_output_var->onBefore();
            $_pinpoint_output_ret = parent::output($_1, $_2, $_3);
            $_pinpoint_output_var->onEnd($_pinpoint_output_ret);
            return $_pinpoint_output_ret;
        } catch (\Exception $e) {
            $_pinpoint_output_var->onException($e);
            throw $e;
        }
    }
    public function noreturn(string $_1, int $_2, array &$_3, $a, $b, $c)
    {
        $_pinpoint_noreturn_var = new \Pinpoint\test\OutputMonitor(__METHOD__, $this, $_1, $_2, $_3, $a, $b, $c);
        $_pinpoint_noreturn_ret = null;
        try {
            $_pinpoint_noreturn_var->onBefore();
            parent::noreturn($_1, $_2, $_3, $a, $b, $c);
            $_pinpoint_noreturn_var->onEnd($_pinpoint_noreturn_ret);
        } catch (\Exception $e) {
            $_pinpoint_noreturn_var->onException($e);
            throw $e;
        }
    }
    protected function checkProtected()
    {
        $_pinpoint_checkProtected_var = new \Pinpoint\test\OutputMonitor(__METHOD__, $this);
        $_pinpoint_checkProtected_ret = null;
        try {
            $_pinpoint_checkProtected_var->onBefore();
            $_pinpoint_checkProtected_ret = parent::checkProtected();
            $_pinpoint_checkProtected_var->onEnd($_pinpoint_checkProtected_ret);
            return $_pinpoint_checkProtected_ret;
        } catch (\Exception $e) {
            $_pinpoint_checkProtected_var->onException($e);
            throw $e;
        }
    }
    protected function checkPrivate()
    {
        $_pinpoint_checkPrivate_var = new \Pinpoint\test\OutputMonitor(__METHOD__, $this);
        $_pinpoint_checkPrivate_ret = null;
        try {
            $_pinpoint_checkPrivate_var->onBefore();
            $_pinpoint_checkPrivate_ret = parent::checkPrivate();
            $_pinpoint_checkPrivate_var->onEnd($_pinpoint_checkPrivate_ret);
            return $_pinpoint_checkPrivate_ret;
        } catch (\Exception $e) {
            $_pinpoint_checkPrivate_var->onException($e);
            throw $e;
        }
    }
}