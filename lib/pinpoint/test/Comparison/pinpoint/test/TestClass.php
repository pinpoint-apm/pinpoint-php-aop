<?php

namespace pinpoint\test;

use App\Class1;
use App\Class2;
use App\Class3 as FooClass;
use App\Class4 as FooClass4;
use pinpoint\test\Proxied_TestClass;
use pinpoint\test\traitTestPlugin;
use pinpoint\test\burden\depress\herb\e\e\f\longNp\victim;
use \over;
abstract class TestClass extends Proxied_TestClass
{
    public function __construct($a, $b, $c)
    {
        $traitTestPlugin___construct_var = new traitTestPlugin(__METHOD__, $this, $a, $b, $c);
        $traitTestPlugin___construct_ret = null;
        try {
            $traitTestPlugin___construct_var->onBefore();
            parent::__construct($a, $b, $c);
            $traitTestPlugin___construct_var->onEnd($traitTestPlugin___construct_ret);
        } catch (\Exception $e) {
            $traitTestPlugin___construct_var->onException($e);
            throw $e;
        }
    }
    public function foo($a, $b, $v, $d) : array
    {
        $traitTestPlugin_foo_var = new traitTestPlugin(__METHOD__, $this, $a, $b, $v, $d);
        $traitTestPlugin_foo_ret = null;
        try {
            $traitTestPlugin_foo_var->onBefore();
            $traitTestPlugin_foo_ret = parent::foo($a, $b, $v, $d);
            $traitTestPlugin_foo_var->onEnd($traitTestPlugin_foo_ret);
            return $traitTestPlugin_foo_ret;
        } catch (\Exception $e) {
            $traitTestPlugin_foo_var->onException($e);
            throw $e;
        }
    }
    public function fooUseYield()
    {
        $traitTestPlugin_fooUseYield_var = new traitTestPlugin(__METHOD__, $this);
        $traitTestPlugin_fooUseYield_ret = null;
        try {
            $traitTestPlugin_fooUseYield_var->onBefore();
            $traitTestPlugin_fooUseYield_ret = parent::fooUseYield();
            $traitTestPlugin_fooUseYield_var->onEnd($traitTestPlugin_fooUseYield_ret);
            return $traitTestPlugin_fooUseYield_ret;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    public function fooNoReturn()
    {
        $traitTestPlugin_fooNoReturn_var = new traitTestPlugin(__METHOD__, $this);
        $traitTestPlugin_fooNoReturn_ret = null;
        try {
            parent::fooNoReturn();
        } catch (\Exception $e) {
            $traitTestPlugin_fooNoReturn_var->onException($e);
            throw $e;
        }
    }
    public function fooNoReturnButReturn()
    {
        $victim_fooNoReturnButReturn_var = new victim(__METHOD__, $this);
        $victim_fooNoReturnButReturn_ret = null;
        try {
            $victim_fooNoReturnButReturn_ret = parent::fooNoReturnButReturn();
            return $victim_fooNoReturnButReturn_ret;
        } catch (\Exception $e) {
            $victim_fooNoReturnButReturn_var->onException($e);
            throw $e;
        }
    }
    public final function fooNaughtyFinal($a, $b, $c)
    {
        $over_fooNaughtyFinal_var = new over(__METHOD__, $this, $a, $b, $c);
        $over_fooNaughtyFinal_ret = null;
        try {
            $over_fooNaughtyFinal_var->onBefore();
            $over_fooNaughtyFinal_ret = parent::fooNaughtyFinal($a, $b, $c);
            $over_fooNaughtyFinal_var->onEnd($over_fooNaughtyFinal_ret);
            return $over_fooNaughtyFinal_ret;
        } catch (\Exception $e) {
            $over_fooNaughtyFinal_var->onException($e);
            throw $e;
        }
    }
    protected function fooTestACPrivate()
    {
        $traitTestPlugin_fooTestACPrivate_var = new traitTestPlugin(__METHOD__, $this);
        $traitTestPlugin_fooTestACPrivate_ret = null;
        try {
            $traitTestPlugin_fooTestACPrivate_ret = parent::fooTestACPrivate();
            return $traitTestPlugin_fooTestACPrivate_ret;
        } catch (\Exception $e) {
            $traitTestPlugin_fooTestACPrivate_var->onException($e);
            throw $e;
        }
    }
    public function fooTestCompatible(class1 $a, class2 $b, FooClass $c, FooClass4 $d)
    {
        $traitTestPlugin_fooTestCompatible_var = new traitTestPlugin(__METHOD__, $this, $a, $b, $c, $d);
        $traitTestPlugin_fooTestCompatible_ret = null;
        try {
            $traitTestPlugin_fooTestCompatible_ret = parent::fooTestCompatible($a, $b, $c, $d);
            return $traitTestPlugin_fooTestCompatible_ret;
        } catch (\Exception $e) {
            $traitTestPlugin_fooTestCompatible_var->onException($e);
            throw $e;
        }
    }
    public function returnNothing() : void
    {
        $traitTestPlugin_returnNothing_var = new traitTestPlugin(__METHOD__, $this);
        $traitTestPlugin_returnNothing_ret = null;
        try {
            $traitTestPlugin_returnNothing_var->onBefore();
            parent::returnNothing();
            $traitTestPlugin_returnNothing_var->onEnd($traitTestPlugin_returnNothing_ret);
        } catch (\Exception $e) {
            $traitTestPlugin_returnNothing_var->onException($e);
            throw $e;
        }
    }
    public function returnNothing_1()
    {
        $traitTestPlugin_returnNothing_1_var = new traitTestPlugin(__METHOD__, $this);
        $traitTestPlugin_returnNothing_1_ret = null;
        try {
            $traitTestPlugin_returnNothing_1_var->onBefore();
            parent::returnNothing_1();
            $traitTestPlugin_returnNothing_1_var->onEnd($traitTestPlugin_returnNothing_1_ret);
        } catch (\Exception $e) {
            $traitTestPlugin_returnNothing_1_var->onException($e);
            throw $e;
        }
    }
}