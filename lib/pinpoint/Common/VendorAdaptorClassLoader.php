<?php declare(strict_types=1);
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

use Composer\Autoload\ClassLoader;
use Exception;

/*
 * Adaptor other classloader
 * mostly expose API :
 *  findFile
 *  loadClass
 */
class VendorAdaptorClassLoader
{
    protected $callOrgFindFile;
    protected $vendor_load_class_func;

    protected function __construct(array $orgLoader)
    {

        $this->vendor_load_class_func =  function (string $clsFullName) use (&$orgLoader){
            return call_user_func($orgLoader,$clsFullName);
        };

        // check loader is ClassLoader
        if($orgLoader[0] instanceof ClassLoader)
        {

            $this->callOrgFindFile = function (string $clsFullName) use (&$orgLoader){
                return $orgLoader[0]->findFile($clsFullName);
            };

        }else if( is_callable($orgLoader) && count($orgLoader) >=2 ){
            /**
             * here hide a case: what if the findFile is private
             * [0]: maybe the class name
             * [1]：maybe the static function name
             * 
             * 1. check [0] , there is findFile function
             */

            if(!method_exists($orgLoader[0],'findFile')){
                throw new Exception("ClassLoader not compatible: no findFile method");
            }

            $this->callOrgFindFile = function (string $clsFullName)  use (&$orgLoader){
                $_loader = new $orgLoader[0];
                $callfindFile = function($name) {
                        return $this->findFile($name);
                };
                return $callfindFile->call($_loader,$clsFullName);
            };

        }else{
            throw new Exception("ClassLoader not compatible: classLoader unknow");
        }

        assert($this->callOrgFindFile);

    }

    public function findFile(string $classFullName):string
    {

        if( is_callable($this->callOrgFindFile) )
        {
            $file = call_user_func($this->callOrgFindFile,$classFullName);
            if ( $file !== false )
            {
                return realpath($file) ?: $file;
            }
        }

        return '';
    }

    /**
     * call vendor loader or other framework defined loader
     * @param $class
     */
    public function loadClass($class)
    {
        if(is_callable($this->vendor_load_class_func)){
            return call_user_func($this->vendor_load_class_func,$class);
        }
        return false;
    }

    /**
     * register pinpoint aop class loader, wrapper vendor class loader
     * @param $classIndex
     * @return bool
     */
    public static function init()
    {
        $loaders = spl_autoload_functions();
        foreach ($loaders as &$loader) {
            if ( is_array($loader) && is_callable($loader) ) {// common ClassLoader style
                
                // unregister composer loader
                spl_autoload_unregister($loader);
                // replace composer loader with aopclassLoader
                $_loader = new self($loader);
                spl_autoload_register(array($_loader,'loadClass'),true,false);
            }
        }
    }

}
