<?php

declare(strict_types=1);
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

namespace Pinpoint\Common;

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
    protected $originClassLoaderFunc;
    protected $vendor_load_class_func;

    protected function __construct(array &$orgLoader, UserClassLoaderInterface $userLoader = null)
    {

        $this->vendor_load_class_func =  function (string $clsFullName) use (&$orgLoader) {
            return call_user_func($orgLoader, $clsFullName);
        };

        // it's a common ClassLoader
        if ($orgLoader[0] instanceof ClassLoader) {

            $this->originClassLoaderFunc = function (string $clsFullName) use (&$orgLoader) {
                return $orgLoader[0]->findFile($clsFullName);
            };
            return;
        }

        //check is `findFile` classLoader
        if ($userLoader != null) {
            $this->originClassLoaderFunc = $userLoader->userClassLoader($orgLoader);
            assert(is_callable($this->originClassLoaderFunc));
            return;
        }

        throw new Exception("unknown classLoader:'$orgLoader'");
    }

    public function findFile(string $classFullName): string
    {
        Logger::Inst()->debug("try to VendorAdaptorClassLoader->findFile:'$classFullName' ");
        if (is_callable($this->originClassLoaderFunc)) {
            $file = call_user_func($this->originClassLoaderFunc, $classFullName);
            if ($file !== false) {
                Logger::Inst()->debug("VendorAdaptorClassLoader->findFile:'$classFullName' ->'$file' ");
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
        if (is_callable($this->vendor_load_class_func)) {
            return call_user_func($this->vendor_load_class_func, $class);
        }
    }

    /**
     * register pinpoint aop class loader, wrapper vendor class loader
     * @param $classIndex
     * @return bool
     */
    public static function enable(UserClassLoaderInterface $u_classLoader = null)
    {
        $loaders = spl_autoload_functions();
        foreach ($loaders as &$olderLoader) {
            if (is_array($olderLoader) && is_callable($olderLoader)) {
                try {
                    // replace composer loader with aopclassLoader
                    $newLoader = new VendorAdaptorClassLoader($olderLoader, $u_classLoader);
                    // unregister composer loader
                    spl_autoload_unregister($olderLoader);
                    spl_autoload_register(array($newLoader, 'loadClass'), true, false);
                } catch (Exception $e) {
                    /**
                     * if current loader not compatible, just ignore it!
                     * why?
                     *  1. pinpoint-php-aop only works on known framework! Exception will not expose to usr
                     *  2. Keep this loader, as it will handled its class. (We could write patch for this loader)
                     *  3. Pinpoint classloader is the highest priority
                     */
                    Logger::Inst()->debug(" re-register pinpointloader failed '$e' ");
                }
            }
        }
    }
}
