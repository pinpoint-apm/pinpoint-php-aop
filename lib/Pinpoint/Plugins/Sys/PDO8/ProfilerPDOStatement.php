<?php
/******************************************************************************
 * Copyright 2020 NAVER Corp.                                                 *
 *                                                                            *
 * Licensed under the Apache License, Version 2.0 (the "License");            *
 * you may not use this file except in compliance with the License.           *
 * You may obtain a copy of the License at                                    *
 *                                                                            *
 *     http://www.apache.org/licenses/LICENSE-2.0                             *
 *                                                                            *
 * Unless required by applicable law or agreed to in writing, software        *
 * distributed under the License is distributed on an "AS IS" BASIS,          *
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.   *
 * See the License for the specific language governing permissions and        *
 * limitations under the License.                                             *
 ******************************************************************************/



namespace Pinpoint\Plugins\Sys\PDO8;
use PDO;

class ProfilerPDOStatement extends \PDOStatement
{
    protected $_instance ;
    protected $_name_list;
    protected $name;
    public function __construct(&$instance)
    {
        $this->_instance = &$instance;
        $this->_name_list=[
            'fetchAll','fetch','nextRowset','getColumnMeta','fetchObject','execute'
        ];
    }

    protected function onBefore()
    {
        pinpoint_start_trace();
        pinpoint_add_clue(PP_INTERCEPTOR_NAME,$this->name);
    }

    protected function onEnd(&$ret)
    {
        pinpoint_end_trace();
    }

    protected function onException($e)
    {
        pinpoint_add_clue(PP_ADD_EXCEPTION,$e->getMessage());
    }


    public function bindValue(string|int $param, mixed $value, int $type = PDO::PARAM_STR): bool
    {
        $args = \pinpoint_get_func_ref_args();
        return $this->profiler(__FUNCTION__,$args);
    }

    public function closeCursor(): bool
    {
        $args = \pinpoint_get_func_ref_args();
        return $this->profiler(__FUNCTION__,$args);
    }

    public function columnCount(): int
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function debugDumpParams(): ?bool
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function errorCode(): ?string
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function errorInfo(): array
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function execute(?array $params = null): bool
    {
        $args = \pinpoint_get_func_ref_args();
        return $this->profiler(__FUNCTION__,$args);
    }

    public function fetch(int $mode = PDO::FETCH_DEFAULT, int $cursorOrientation = PDO::FETCH_ORI_NEXT, int $cursorOffset = 0): mixed
    {
        $args = \pinpoint_get_func_ref_args();
        return $this->profiler(__FUNCTION__,$args);
    }
    // not support php5 any more
    public function fetchAll(int $mode = PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        $_args = \pinpoint_get_func_ref_args();
        return $this->profiler(__FUNCTION__,$_args);
    }

    public function fetchColumn(int $column = 0): mixed
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function fetchObject(?string $class = "stdClass", array $constructorArgs = []): object|false
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function getAttribute(int $name): mixed
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function getColumnMeta(int $column): array|false
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function nextRowset(): bool
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function rowCount(): int
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function setAttribute(int $attribute, mixed $value): bool
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function setFetchMode($mode,mixed ...$args)
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }


    public function bindParam (string|int $param, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    public function bindColumn (string|int $column, mixed &$var, int $type = PDO::PARAM_STR, int $maxLength = 0, mixed $driverOptions = null): bool
    {
       $args = \pinpoint_get_func_ref_args();
       return $this->profiler(__FUNCTION__,$args);
    }

    private function profiler($name,&$arguments)
    {
        if(!in_array($name ,$this->_name_list))
        {
            return call_user_func_array([$this->_instance,$name],$arguments);
        }else{
            try{
                $this->name = "PDOStatement::".$name;
                $this->args = &$arguments;
                $this->onBefore();
                $ret = call_user_func_array([$this->_instance,$name],$arguments);
                $this->onEnd($ret);
                return $ret;
            }catch (\Exception $e){
                $this->onException($e);
                $ret = null;
                $this->onEnd($ret);
                throw $e;
            }
        }

    }

}
