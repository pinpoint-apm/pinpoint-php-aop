<?php
/**
 * Copyright 2020-present NAVER Corp.
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


namespace pinpoint\Common;

class AopClassMap
{
    protected $index_file_path=AOP_CACHE_DIR.'__class_index_table';
    private $cached = false;
    private $classLoaderMap = [];

    public function __construct()
    {

    }

    public function updateSelf()
    {
        if( ( !defined('PINPOINT_USE_CACHE') ||
            stristr(PINPOINT_USE_CACHE,"NO") === false ) &&
            file_exists($this->index_file_path) )
        {
            $this->classLoaderMap = unserialize(file_get_contents($this->index_file_path));
            $this->cached = true;
            return false;
        }else{
            return true;
        }
    }

    public function persistenceClassMapping()
    {
        if(!$this->cached){
            $context = serialize($this->classLoaderMap);
            Util::flushStr2File($context,$this->index_file_path);
        }
    }

    public function findFile($classFullName)
    {
        if(isset($this->classLoaderMap[$classFullName]))
        {
            return $this->classLoaderMap[$classFullName];
        }
        return null;
    }

    public  function insertMapping($cl,$file)
    {
        $this->classLoaderMap[$cl] = $file;
    }

    public function getLoadeMap()
    {
        return $this->classLoaderMap;
    }

}