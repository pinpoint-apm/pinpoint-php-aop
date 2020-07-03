<?php

namespace app\Foo;

use pinpoint\commPlugins;
class Exception extends \Exception
{
    public function __construct($message = null, $code = null, $previous = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins___construct_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins___construct_var->onBefore();
            $commPlugins___construct_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins___construct_var->onEnd($commPlugins___construct_ret);
            return $commPlugins___construct_ret;
        } catch (\Exception $e) {
            $commPlugins___construct_var->onException($e);
            throw $e;
        }
    }
    public function __wakeup()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins___wakeup_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins___wakeup_var->onBefore();
            $commPlugins___wakeup_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins___wakeup_var->onEnd($commPlugins___wakeup_ret);
            return $commPlugins___wakeup_ret;
        } catch (\Exception $e) {
            $commPlugins___wakeup_var->onException($e);
            throw $e;
        }
    }
    public function __toString()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins___toString_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins___toString_var->onBefore();
            $commPlugins___toString_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins___toString_var->onEnd($commPlugins___toString_ret);
            return $commPlugins___toString_ret;
        } catch (\Exception $e) {
            $commPlugins___toString_var->onException($e);
            throw $e;
        }
    }
}
class PDOStatement extends \PDOStatement
{
    public function execute($bound_input_params = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_execute_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_execute_var->onBefore();
            $commPlugins_execute_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_execute_var->onEnd($commPlugins_execute_ret);
            return $commPlugins_execute_ret;
        } catch (\Exception $e) {
            $commPlugins_execute_var->onException($e);
            throw $e;
        }
    }
    public function fetch($how = null, $orientation = null, $offset = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_fetch_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_fetch_var->onBefore();
            $commPlugins_fetch_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_fetch_var->onEnd($commPlugins_fetch_ret);
            return $commPlugins_fetch_ret;
        } catch (\Exception $e) {
            $commPlugins_fetch_var->onException($e);
            throw $e;
        }
    }
    public function bindParam($paramno, &$param, $type = null, $maxlen = null, $driverdata = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_bindParam_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_bindParam_var->onBefore();
            $commPlugins_bindParam_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_bindParam_var->onEnd($commPlugins_bindParam_ret);
            return $commPlugins_bindParam_ret;
        } catch (\Exception $e) {
            $commPlugins_bindParam_var->onException($e);
            throw $e;
        }
    }
    public function bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_bindColumn_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_bindColumn_var->onBefore();
            $commPlugins_bindColumn_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_bindColumn_var->onEnd($commPlugins_bindColumn_ret);
            return $commPlugins_bindColumn_ret;
        } catch (\Exception $e) {
            $commPlugins_bindColumn_var->onException($e);
            throw $e;
        }
    }
    public function bindValue($paramno, $param, $type = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_bindValue_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_bindValue_var->onBefore();
            $commPlugins_bindValue_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_bindValue_var->onEnd($commPlugins_bindValue_ret);
            return $commPlugins_bindValue_ret;
        } catch (\Exception $e) {
            $commPlugins_bindValue_var->onException($e);
            throw $e;
        }
    }
    public function rowCount()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_rowCount_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_rowCount_var->onBefore();
            $commPlugins_rowCount_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_rowCount_var->onEnd($commPlugins_rowCount_ret);
            return $commPlugins_rowCount_ret;
        } catch (\Exception $e) {
            $commPlugins_rowCount_var->onException($e);
            throw $e;
        }
    }
    public function fetchColumn($column_number = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_fetchColumn_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_fetchColumn_var->onBefore();
            $commPlugins_fetchColumn_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_fetchColumn_var->onEnd($commPlugins_fetchColumn_ret);
            return $commPlugins_fetchColumn_ret;
        } catch (\Exception $e) {
            $commPlugins_fetchColumn_var->onException($e);
            throw $e;
        }
    }
    public function fetchAll($how = null, $class_name = null, $ctor_args = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_fetchAll_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_fetchAll_var->onBefore();
            $commPlugins_fetchAll_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_fetchAll_var->onEnd($commPlugins_fetchAll_ret);
            return $commPlugins_fetchAll_ret;
        } catch (\Exception $e) {
            $commPlugins_fetchAll_var->onException($e);
            throw $e;
        }
    }
    public function fetchObject($class_name = null, $ctor_args = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_fetchObject_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_fetchObject_var->onBefore();
            $commPlugins_fetchObject_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_fetchObject_var->onEnd($commPlugins_fetchObject_ret);
            return $commPlugins_fetchObject_ret;
        } catch (\Exception $e) {
            $commPlugins_fetchObject_var->onException($e);
            throw $e;
        }
    }
    public function errorCode()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_errorCode_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_errorCode_var->onBefore();
            $commPlugins_errorCode_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_errorCode_var->onEnd($commPlugins_errorCode_ret);
            return $commPlugins_errorCode_ret;
        } catch (\Exception $e) {
            $commPlugins_errorCode_var->onException($e);
            throw $e;
        }
    }
    public function errorInfo()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_errorInfo_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_errorInfo_var->onBefore();
            $commPlugins_errorInfo_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_errorInfo_var->onEnd($commPlugins_errorInfo_ret);
            return $commPlugins_errorInfo_ret;
        } catch (\Exception $e) {
            $commPlugins_errorInfo_var->onException($e);
            throw $e;
        }
    }
    public function setAttribute($attribute, $value)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_setAttribute_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_setAttribute_var->onBefore();
            $commPlugins_setAttribute_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_setAttribute_var->onEnd($commPlugins_setAttribute_ret);
            return $commPlugins_setAttribute_ret;
        } catch (\Exception $e) {
            $commPlugins_setAttribute_var->onException($e);
            throw $e;
        }
    }
    public function getAttribute($attribute)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_getAttribute_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_getAttribute_var->onBefore();
            $commPlugins_getAttribute_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_getAttribute_var->onEnd($commPlugins_getAttribute_ret);
            return $commPlugins_getAttribute_ret;
        } catch (\Exception $e) {
            $commPlugins_getAttribute_var->onException($e);
            throw $e;
        }
    }
    public function columnCount()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_columnCount_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_columnCount_var->onBefore();
            $commPlugins_columnCount_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_columnCount_var->onEnd($commPlugins_columnCount_ret);
            return $commPlugins_columnCount_ret;
        } catch (\Exception $e) {
            $commPlugins_columnCount_var->onException($e);
            throw $e;
        }
    }
    public function getColumnMeta($column)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_getColumnMeta_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_getColumnMeta_var->onBefore();
            $commPlugins_getColumnMeta_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_getColumnMeta_var->onEnd($commPlugins_getColumnMeta_ret);
            return $commPlugins_getColumnMeta_ret;
        } catch (\Exception $e) {
            $commPlugins_getColumnMeta_var->onException($e);
            throw $e;
        }
    }
    public function setFetchMode($mode, $params = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_setFetchMode_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_setFetchMode_var->onBefore();
            $commPlugins_setFetchMode_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_setFetchMode_var->onEnd($commPlugins_setFetchMode_ret);
            return $commPlugins_setFetchMode_ret;
        } catch (\Exception $e) {
            $commPlugins_setFetchMode_var->onException($e);
            throw $e;
        }
    }
    public function nextRowset()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_nextRowset_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_nextRowset_var->onBefore();
            $commPlugins_nextRowset_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_nextRowset_var->onEnd($commPlugins_nextRowset_ret);
            return $commPlugins_nextRowset_ret;
        } catch (\Exception $e) {
            $commPlugins_nextRowset_var->onException($e);
            throw $e;
        }
    }
    public function closeCursor()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_closeCursor_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_closeCursor_var->onBefore();
            $commPlugins_closeCursor_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_closeCursor_var->onEnd($commPlugins_closeCursor_ret);
            return $commPlugins_closeCursor_ret;
        } catch (\Exception $e) {
            $commPlugins_closeCursor_var->onException($e);
            throw $e;
        }
    }
    public function debugDumpParams()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_debugDumpParams_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_debugDumpParams_var->onBefore();
            $commPlugins_debugDumpParams_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_debugDumpParams_var->onEnd($commPlugins_debugDumpParams_ret);
            return $commPlugins_debugDumpParams_ret;
        } catch (\Exception $e) {
            $commPlugins_debugDumpParams_var->onException($e);
            throw $e;
        }
    }
}
class PDO extends \PDO
{
    public function __construct($dsn, $username = null, $passwd = null, $options = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins___construct_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins___construct_var->onBefore();
            $commPlugins___construct_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins___construct_var->onEnd($commPlugins___construct_ret);
            return $commPlugins___construct_ret;
        } catch (\Exception $e) {
            $commPlugins___construct_var->onException($e);
            throw $e;
        }
    }
    public function prepare($statement, $options = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_prepare_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_prepare_var->onBefore();
            $commPlugins_prepare_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_prepare_var->onEnd($commPlugins_prepare_ret);
            return $commPlugins_prepare_ret;
        } catch (\Exception $e) {
            $commPlugins_prepare_var->onException($e);
            throw $e;
        }
    }
    public function beginTransaction()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_beginTransaction_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_beginTransaction_var->onBefore();
            $commPlugins_beginTransaction_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_beginTransaction_var->onEnd($commPlugins_beginTransaction_ret);
            return $commPlugins_beginTransaction_ret;
        } catch (\Exception $e) {
            $commPlugins_beginTransaction_var->onException($e);
            throw $e;
        }
    }
    public function commit()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_commit_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_commit_var->onBefore();
            $commPlugins_commit_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_commit_var->onEnd($commPlugins_commit_ret);
            return $commPlugins_commit_ret;
        } catch (\Exception $e) {
            $commPlugins_commit_var->onException($e);
            throw $e;
        }
    }
    public function rollBack()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_rollBack_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_rollBack_var->onBefore();
            $commPlugins_rollBack_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_rollBack_var->onEnd($commPlugins_rollBack_ret);
            return $commPlugins_rollBack_ret;
        } catch (\Exception $e) {
            $commPlugins_rollBack_var->onException($e);
            throw $e;
        }
    }
    public function inTransaction()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_inTransaction_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_inTransaction_var->onBefore();
            $commPlugins_inTransaction_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_inTransaction_var->onEnd($commPlugins_inTransaction_ret);
            return $commPlugins_inTransaction_ret;
        } catch (\Exception $e) {
            $commPlugins_inTransaction_var->onException($e);
            throw $e;
        }
    }
    public function setAttribute($attribute, $value)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_setAttribute_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_setAttribute_var->onBefore();
            $commPlugins_setAttribute_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_setAttribute_var->onEnd($commPlugins_setAttribute_ret);
            return $commPlugins_setAttribute_ret;
        } catch (\Exception $e) {
            $commPlugins_setAttribute_var->onException($e);
            throw $e;
        }
    }
    public function exec($query)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_exec_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_exec_var->onBefore();
            $commPlugins_exec_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_exec_var->onEnd($commPlugins_exec_ret);
            return $commPlugins_exec_ret;
        } catch (\Exception $e) {
            $commPlugins_exec_var->onException($e);
            throw $e;
        }
    }
    public function query()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_query_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_query_var->onBefore();
            $commPlugins_query_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_query_var->onEnd($commPlugins_query_ret);
            return $commPlugins_query_ret;
        } catch (\Exception $e) {
            $commPlugins_query_var->onException($e);
            throw $e;
        }
    }
    public function lastInsertId($seqname = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_lastInsertId_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_lastInsertId_var->onBefore();
            $commPlugins_lastInsertId_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_lastInsertId_var->onEnd($commPlugins_lastInsertId_ret);
            return $commPlugins_lastInsertId_ret;
        } catch (\Exception $e) {
            $commPlugins_lastInsertId_var->onException($e);
            throw $e;
        }
    }
    public function errorCode()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_errorCode_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_errorCode_var->onBefore();
            $commPlugins_errorCode_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_errorCode_var->onEnd($commPlugins_errorCode_ret);
            return $commPlugins_errorCode_ret;
        } catch (\Exception $e) {
            $commPlugins_errorCode_var->onException($e);
            throw $e;
        }
    }
    public function errorInfo()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_errorInfo_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_errorInfo_var->onBefore();
            $commPlugins_errorInfo_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_errorInfo_var->onEnd($commPlugins_errorInfo_ret);
            return $commPlugins_errorInfo_ret;
        } catch (\Exception $e) {
            $commPlugins_errorInfo_var->onException($e);
            throw $e;
        }
    }
    public function getAttribute($attribute)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_getAttribute_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_getAttribute_var->onBefore();
            $commPlugins_getAttribute_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_getAttribute_var->onEnd($commPlugins_getAttribute_ret);
            return $commPlugins_getAttribute_ret;
        } catch (\Exception $e) {
            $commPlugins_getAttribute_var->onException($e);
            throw $e;
        }
    }
    public function quote($string, $paramtype = null)
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_quote_var = new commPlugins(__METHOD__, $this, $args);
        try {
            $commPlugins_quote_var->onBefore();
            $commPlugins_quote_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_quote_var->onEnd($commPlugins_quote_ret);
            return $commPlugins_quote_ret;
        } catch (\Exception $e) {
            $commPlugins_quote_var->onException($e);
            throw $e;
        }
    }
    public static function getAvailableDrivers()
    {
        $args = \pinpoint_get_func_ref_args();
        $commPlugins_getAvailableDrivers_var = new commPlugins(__METHOD__, null, $args);
        try {
            $commPlugins_getAvailableDrivers_var->onBefore();
            $commPlugins_getAvailableDrivers_ret = call_user_func_array(array('parent', __FUNCTION__), $args);
            $commPlugins_getAvailableDrivers_var->onEnd($commPlugins_getAvailableDrivers_ret);
            return $commPlugins_getAvailableDrivers_ret;
        } catch (\Exception $e) {
            $commPlugins_getAvailableDrivers_var->onException($e);
            throw $e;
        }
    }
}
function curl_init($url = null)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_init_var = new commPlugins('curl_init', null, $args);
    try {
        $commPlugins_curl_init_var->onBefore();
        $commPlugins_curl_init_ret = call_user_func_array('curl_init', $args);
        $commPlugins_curl_init_var->onEnd($commPlugins_curl_init_ret);
        return $commPlugins_curl_init_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_init_var->onException($e);
        throw $e;
    }
}
function curl_copy_handle($ch)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_copy_handle_var = new commPlugins('curl_copy_handle', null, $args);
    try {
        $commPlugins_curl_copy_handle_var->onBefore();
        $commPlugins_curl_copy_handle_ret = call_user_func_array('curl_copy_handle', $args);
        $commPlugins_curl_copy_handle_var->onEnd($commPlugins_curl_copy_handle_ret);
        return $commPlugins_curl_copy_handle_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_copy_handle_var->onException($e);
        throw $e;
    }
}
function curl_version($version = null)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_version_var = new commPlugins('curl_version', null, $args);
    try {
        $commPlugins_curl_version_var->onBefore();
        $commPlugins_curl_version_ret = call_user_func_array('curl_version', $args);
        $commPlugins_curl_version_var->onEnd($commPlugins_curl_version_ret);
        return $commPlugins_curl_version_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_version_var->onException($e);
        throw $e;
    }
}
function curl_setopt($ch, $option, $value)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_setopt_var = new commPlugins('curl_setopt', null, $args);
    try {
        $commPlugins_curl_setopt_var->onBefore();
        $commPlugins_curl_setopt_ret = call_user_func_array('curl_setopt', $args);
        $commPlugins_curl_setopt_var->onEnd($commPlugins_curl_setopt_ret);
        return $commPlugins_curl_setopt_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_setopt_var->onException($e);
        throw $e;
    }
}
function curl_setopt_array($ch, array $options)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_setopt_array_var = new commPlugins('curl_setopt_array', null, $args);
    try {
        $commPlugins_curl_setopt_array_var->onBefore();
        $commPlugins_curl_setopt_array_ret = call_user_func_array('curl_setopt_array', $args);
        $commPlugins_curl_setopt_array_var->onEnd($commPlugins_curl_setopt_array_ret);
        return $commPlugins_curl_setopt_array_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_setopt_array_var->onException($e);
        throw $e;
    }
}
function curl_exec($ch)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_exec_var = new commPlugins('curl_exec', null, $args);
    try {
        $commPlugins_curl_exec_var->onBefore();
        $commPlugins_curl_exec_ret = call_user_func_array('curl_exec', $args);
        $commPlugins_curl_exec_var->onEnd($commPlugins_curl_exec_ret);
        return $commPlugins_curl_exec_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_exec_var->onException($e);
        throw $e;
    }
}
function curl_getinfo($ch, $option = null)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_getinfo_var = new commPlugins('curl_getinfo', null, $args);
    try {
        $commPlugins_curl_getinfo_var->onBefore();
        $commPlugins_curl_getinfo_ret = call_user_func_array('curl_getinfo', $args);
        $commPlugins_curl_getinfo_var->onEnd($commPlugins_curl_getinfo_ret);
        return $commPlugins_curl_getinfo_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_getinfo_var->onException($e);
        throw $e;
    }
}
function curl_error($ch)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_error_var = new commPlugins('curl_error', null, $args);
    try {
        $commPlugins_curl_error_var->onBefore();
        $commPlugins_curl_error_ret = call_user_func_array('curl_error', $args);
        $commPlugins_curl_error_var->onEnd($commPlugins_curl_error_ret);
        return $commPlugins_curl_error_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_error_var->onException($e);
        throw $e;
    }
}
function curl_errno($ch)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_errno_var = new commPlugins('curl_errno', null, $args);
    try {
        $commPlugins_curl_errno_var->onBefore();
        $commPlugins_curl_errno_ret = call_user_func_array('curl_errno', $args);
        $commPlugins_curl_errno_var->onEnd($commPlugins_curl_errno_ret);
        return $commPlugins_curl_errno_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_errno_var->onException($e);
        throw $e;
    }
}
function curl_close($ch)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_close_var = new commPlugins('curl_close', null, $args);
    try {
        $commPlugins_curl_close_var->onBefore();
        $commPlugins_curl_close_ret = call_user_func_array('curl_close', $args);
        $commPlugins_curl_close_var->onEnd($commPlugins_curl_close_ret);
        return $commPlugins_curl_close_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_close_var->onException($e);
        throw $e;
    }
}
function curl_strerror($errornum)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_strerror_var = new commPlugins('curl_strerror', null, $args);
    try {
        $commPlugins_curl_strerror_var->onBefore();
        $commPlugins_curl_strerror_ret = call_user_func_array('curl_strerror', $args);
        $commPlugins_curl_strerror_var->onEnd($commPlugins_curl_strerror_ret);
        return $commPlugins_curl_strerror_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_strerror_var->onException($e);
        throw $e;
    }
}
function curl_multi_strerror($errornum)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_strerror_var = new commPlugins('curl_multi_strerror', null, $args);
    try {
        $commPlugins_curl_multi_strerror_var->onBefore();
        $commPlugins_curl_multi_strerror_ret = call_user_func_array('curl_multi_strerror', $args);
        $commPlugins_curl_multi_strerror_var->onEnd($commPlugins_curl_multi_strerror_ret);
        return $commPlugins_curl_multi_strerror_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_strerror_var->onException($e);
        throw $e;
    }
}
function curl_reset($ch)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_reset_var = new commPlugins('curl_reset', null, $args);
    try {
        $commPlugins_curl_reset_var->onBefore();
        $commPlugins_curl_reset_ret = call_user_func_array('curl_reset', $args);
        $commPlugins_curl_reset_var->onEnd($commPlugins_curl_reset_ret);
        return $commPlugins_curl_reset_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_reset_var->onException($e);
        throw $e;
    }
}
function curl_escape($ch, $str)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_escape_var = new commPlugins('curl_escape', null, $args);
    try {
        $commPlugins_curl_escape_var->onBefore();
        $commPlugins_curl_escape_ret = call_user_func_array('curl_escape', $args);
        $commPlugins_curl_escape_var->onEnd($commPlugins_curl_escape_ret);
        return $commPlugins_curl_escape_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_escape_var->onException($e);
        throw $e;
    }
}
function curl_unescape($ch, $str)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_unescape_var = new commPlugins('curl_unescape', null, $args);
    try {
        $commPlugins_curl_unescape_var->onBefore();
        $commPlugins_curl_unescape_ret = call_user_func_array('curl_unescape', $args);
        $commPlugins_curl_unescape_var->onEnd($commPlugins_curl_unescape_ret);
        return $commPlugins_curl_unescape_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_unescape_var->onException($e);
        throw $e;
    }
}
function curl_pause($ch, $bitmask)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_pause_var = new commPlugins('curl_pause', null, $args);
    try {
        $commPlugins_curl_pause_var->onBefore();
        $commPlugins_curl_pause_ret = call_user_func_array('curl_pause', $args);
        $commPlugins_curl_pause_var->onEnd($commPlugins_curl_pause_ret);
        return $commPlugins_curl_pause_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_pause_var->onException($e);
        throw $e;
    }
}
function curl_multi_init()
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_init_var = new commPlugins('curl_multi_init', null, $args);
    try {
        $commPlugins_curl_multi_init_var->onBefore();
        $commPlugins_curl_multi_init_ret = call_user_func_array('curl_multi_init', $args);
        $commPlugins_curl_multi_init_var->onEnd($commPlugins_curl_multi_init_ret);
        return $commPlugins_curl_multi_init_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_init_var->onException($e);
        throw $e;
    }
}
function curl_multi_add_handle($mh, $ch)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_add_handle_var = new commPlugins('curl_multi_add_handle', null, $args);
    try {
        $commPlugins_curl_multi_add_handle_var->onBefore();
        $commPlugins_curl_multi_add_handle_ret = call_user_func_array('curl_multi_add_handle', $args);
        $commPlugins_curl_multi_add_handle_var->onEnd($commPlugins_curl_multi_add_handle_ret);
        return $commPlugins_curl_multi_add_handle_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_add_handle_var->onException($e);
        throw $e;
    }
}
function curl_multi_remove_handle($mh, $ch)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_remove_handle_var = new commPlugins('curl_multi_remove_handle', null, $args);
    try {
        $commPlugins_curl_multi_remove_handle_var->onBefore();
        $commPlugins_curl_multi_remove_handle_ret = call_user_func_array('curl_multi_remove_handle', $args);
        $commPlugins_curl_multi_remove_handle_var->onEnd($commPlugins_curl_multi_remove_handle_ret);
        return $commPlugins_curl_multi_remove_handle_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_remove_handle_var->onException($e);
        throw $e;
    }
}
function curl_multi_select($mh, $timeout = null)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_select_var = new commPlugins('curl_multi_select', null, $args);
    try {
        $commPlugins_curl_multi_select_var->onBefore();
        $commPlugins_curl_multi_select_ret = call_user_func_array('curl_multi_select', $args);
        $commPlugins_curl_multi_select_var->onEnd($commPlugins_curl_multi_select_ret);
        return $commPlugins_curl_multi_select_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_select_var->onException($e);
        throw $e;
    }
}
function curl_multi_exec($mh, &$still_running = null)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_exec_var = new commPlugins('curl_multi_exec', null, $args);
    try {
        $commPlugins_curl_multi_exec_var->onBefore();
        $commPlugins_curl_multi_exec_ret = call_user_func_array('curl_multi_exec', $args);
        $commPlugins_curl_multi_exec_var->onEnd($commPlugins_curl_multi_exec_ret);
        return $commPlugins_curl_multi_exec_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_exec_var->onException($e);
        throw $e;
    }
}
function curl_multi_getcontent($ch)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_getcontent_var = new commPlugins('curl_multi_getcontent', null, $args);
    try {
        $commPlugins_curl_multi_getcontent_var->onBefore();
        $commPlugins_curl_multi_getcontent_ret = call_user_func_array('curl_multi_getcontent', $args);
        $commPlugins_curl_multi_getcontent_var->onEnd($commPlugins_curl_multi_getcontent_ret);
        return $commPlugins_curl_multi_getcontent_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_getcontent_var->onException($e);
        throw $e;
    }
}
function curl_multi_info_read($mh, &$msgs_in_queue = null)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_info_read_var = new commPlugins('curl_multi_info_read', null, $args);
    try {
        $commPlugins_curl_multi_info_read_var->onBefore();
        $commPlugins_curl_multi_info_read_ret = call_user_func_array('curl_multi_info_read', $args);
        $commPlugins_curl_multi_info_read_var->onEnd($commPlugins_curl_multi_info_read_ret);
        return $commPlugins_curl_multi_info_read_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_info_read_var->onException($e);
        throw $e;
    }
}
function curl_multi_close($mh)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_close_var = new commPlugins('curl_multi_close', null, $args);
    try {
        $commPlugins_curl_multi_close_var->onBefore();
        $commPlugins_curl_multi_close_ret = call_user_func_array('curl_multi_close', $args);
        $commPlugins_curl_multi_close_var->onEnd($commPlugins_curl_multi_close_ret);
        return $commPlugins_curl_multi_close_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_close_var->onException($e);
        throw $e;
    }
}
function curl_multi_setopt($sh, $option, $value)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_multi_setopt_var = new commPlugins('curl_multi_setopt', null, $args);
    try {
        $commPlugins_curl_multi_setopt_var->onBefore();
        $commPlugins_curl_multi_setopt_ret = call_user_func_array('curl_multi_setopt', $args);
        $commPlugins_curl_multi_setopt_var->onEnd($commPlugins_curl_multi_setopt_ret);
        return $commPlugins_curl_multi_setopt_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_multi_setopt_var->onException($e);
        throw $e;
    }
}
function curl_share_init()
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_share_init_var = new commPlugins('curl_share_init', null, $args);
    try {
        $commPlugins_curl_share_init_var->onBefore();
        $commPlugins_curl_share_init_ret = call_user_func_array('curl_share_init', $args);
        $commPlugins_curl_share_init_var->onEnd($commPlugins_curl_share_init_ret);
        return $commPlugins_curl_share_init_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_share_init_var->onException($e);
        throw $e;
    }
}
function curl_share_close($sh)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_share_close_var = new commPlugins('curl_share_close', null, $args);
    try {
        $commPlugins_curl_share_close_var->onBefore();
        $commPlugins_curl_share_close_ret = call_user_func_array('curl_share_close', $args);
        $commPlugins_curl_share_close_var->onEnd($commPlugins_curl_share_close_ret);
        return $commPlugins_curl_share_close_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_share_close_var->onException($e);
        throw $e;
    }
}
function curl_share_setopt($sh, $option, $value)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_share_setopt_var = new commPlugins('curl_share_setopt', null, $args);
    try {
        $commPlugins_curl_share_setopt_var->onBefore();
        $commPlugins_curl_share_setopt_ret = call_user_func_array('curl_share_setopt', $args);
        $commPlugins_curl_share_setopt_var->onEnd($commPlugins_curl_share_setopt_ret);
        return $commPlugins_curl_share_setopt_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_share_setopt_var->onException($e);
        throw $e;
    }
}
function curl_file_create($filename, $mimetype = null, $postname = null)
{
    $args = \pinpoint_get_func_ref_args();
    $commPlugins_curl_file_create_var = new commPlugins('curl_file_create', null, $args);
    try {
        $commPlugins_curl_file_create_var->onBefore();
        $commPlugins_curl_file_create_ret = call_user_func_array('curl_file_create', $args);
        $commPlugins_curl_file_create_var->onEnd($commPlugins_curl_file_create_ret);
        return $commPlugins_curl_file_create_ret;
    } catch (\Exception $e) {
        $commPlugins_curl_file_create_var->onException($e);
        throw $e;
    }
}