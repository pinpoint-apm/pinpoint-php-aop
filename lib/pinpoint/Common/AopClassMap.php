<?php

namespace pinpoint\Common;

use pinpoint\Common\Util;

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

        if( ( !defined('PINPOINT_ENV') ||
            stristr(PINPOINT_ENV,"dev") === false ) &&
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

    public  function findFile($classFullName)
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

}