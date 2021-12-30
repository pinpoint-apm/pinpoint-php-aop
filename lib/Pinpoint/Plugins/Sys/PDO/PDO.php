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
namespace Pinpoint\Plugins\Sys\PDO;

class PDO extends \PDO
{
    public $dsn;

    public function __construct ($dsn, $username=null, $passwd=null, $options=[])
    {
        $this->dsn = $dsn;
        parent::__construct($dsn, $username, $passwd, $options);
    }

    public function query(string $query, $fetchMode = null, mixed ...$fetchModeArgs)
    {
        $args = \pinpoint_get_func_ref_args();
        $var = new PreparePlugin("PDO::query",$this,...$args);
        try{
            $var->onBefore();
            $ret = parent::query(...$args);
            $var->onEnd($ret);
            return $ret;
        }catch (\Exception $e){
            $var->onException($e);
            throw new \Exception($e);
        }
    }


    public function exec($statement)
    {
        $var = new PDOExec("PDO::exec",$this,$statement);
        try{
            $var->onBefore();
            $ret = parent::exec($statement);
            $var->onEnd($ret);
            return $ret;
        }catch (\Exception $e){
            $var->onException($e);
            throw new \Exception($e);
        }
    }


    public function prepare($statement,  $driver_options = array())
    {
        $var = new PreparePlugin("PDO::prepare",$this,$statement,$driver_options);
        try{
            $var->onBefore();
            $ret = parent::prepare($statement,$driver_options);
            $var->onEnd($ret);
            return $ret;
        }catch (\Exception $e){
            $var->onException($e);
            throw new \Exception($e);
        }
    }

}