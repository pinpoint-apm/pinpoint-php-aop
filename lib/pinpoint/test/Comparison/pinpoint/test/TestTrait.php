<?php

namespace pinpoint\test;

use pinpoint\test\traitTestPlugin;
trait TestTrait
{
    use Proxied_TestTrait {
        Proxied_TestTrait::getReturnType as Proxied_TestTrait_getReturnType;
    }
    function getReturnType()
    {
        $traitTestPlugin_getReturnType_var = new traitTestPlugin(__METHOD__, $this);
        $traitTestPlugin_getReturnType_ret = null;
        try {
            $traitTestPlugin_getReturnType_var->onBefore();
            $this->Proxied_TestTrait_getReturnType();
            $traitTestPlugin_getReturnType_var->onEnd($traitTestPlugin_getReturnType_ret);
        } catch (\Exception $e) {
            $traitTestPlugin_getReturnType_var->onException($e);
            throw $e;
        }
    }
}