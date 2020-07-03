<?php
/**
 * Copyright 2019 NAVER Corp.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

/**
 * @todo
 * This draft file is intended for internal func, try to support in next version
 */

namespace pinpoint\Common;


class WrapperInternalClass
{
    public static $originName ='PDO';
    private $objInstance;
    public function __construct(...$args)
    {
        $oReflectionClass = new ReflectionClass('PDO');
        $this->objInstance = $oReflectionClass->newInstance(...$args);
    }

    public function __call($name, $arguments)
    {

        return call_user_func_array(array($this->objInstance,$name),$arguments);
    }

    public static  function __callStatic($name, $args)
    {
        try
        {

            $ret = call_user_func_array(array(self::$originName,$name),$args);
        }catch (Exception $e)
        {
            throw new \Exception($name." not find");
        }

        return $ret;
    }
}