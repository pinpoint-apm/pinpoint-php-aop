<?php

namespace pinpoint\test;

use pinpoint\test\traitTestPlugin;
class PDO extends \PDO
{
    public function query()
    {
        $args = debug_backtrace()[0]['args'];
        $traitTestPlugin_query_var = new traitTestPlugin(__METHOD__, $this, $args);
        try {
            $traitTestPlugin_query_var->onBefore();
            $traitTestPlugin_query_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $traitTestPlugin_query_var->onEnd($traitTestPlugin_query_ret);
            return $traitTestPlugin_query_ret;
        } catch (\Exception $e) {
            $traitTestPlugin_query_var->onException($e);
            throw $e;
        }
    }
}
function curl_exec($ch)
{
    $args = debug_backtrace()[0]['args'];
    $traitTestPlugin_curl_exec_var = new traitTestPlugin('curl_exec', null, $args);
    try {
        $traitTestPlugin_curl_exec_var->onBefore();
        $traitTestPlugin_curl_exec_ret = call_user_func_array('curl_exec', $args);
        $traitTestPlugin_curl_exec_var->onEnd($traitTestPlugin_curl_exec_ret);
        return $traitTestPlugin_curl_exec_ret;
    } catch (\Exception $e) {
        $traitTestPlugin_curl_exec_var->onException($e);
        throw $e;
    }
}