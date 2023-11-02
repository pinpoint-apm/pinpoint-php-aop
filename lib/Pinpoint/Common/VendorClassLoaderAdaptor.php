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

use Exception;

/*
 * Adaptor other classloader
 *  findFile
 *  loadClass
 */

class VendorClassLoaderAdaptor
{
    private static $inst = null;
    private ClassLoaderFactory $classLoaderFactory;

    private function __construct()
    {
        $this->classLoaderFactory = new ClassLoaderFactory();
    }

    public static function Inst()
    {
        if (!self::$inst) {
            self::$inst = new static();
        }
        return self::$inst;
    }

    public function setUserFindClass(UserFrameworkInterface $userFindClass = null)
    {
        $this->classLoaderFactory->setUserFindClass($userFindClass);
    }

    /**
     * register pinpoint aop class loader, wrapper vendor class loader
     * @param $classIndex
     * @return bool
     */
    public function start()
    {
        $loaders = spl_autoload_functions();
        foreach ($loaders as &$olderLoader) {
            if (is_array($olderLoader) && is_callable($olderLoader)) {
                try {
                    $newLoader = $this->classLoaderFactory->createLoader($olderLoader);
                    spl_autoload_unregister($olderLoader);
                    spl_autoload_register($newLoader->getClassLoader(), true, false);
                } catch (Exception $e) {
                    Logger::Inst()->debug(" re-register pinpointloader failed '$e' ");
                }
            }
        }
    }

    /**
     * locate a class (via  VendorClassLoaderAdaptor)
     * @param $class
     * @return bool|string
     */
    public function findFileViaSpl(string $className): string
    {
        $splLoaders = spl_autoload_functions();
        foreach ($splLoaders as &$loader) {
            if (is_array($loader)) {
                $vendorLoader = $loader[0];
                assert(is_a($vendorLoader, StanderClassLoader::class), get_class($vendorLoader));
                $address = $vendorLoader->findFile($className);
                if ($address) {
                    return realpath($address);
                }
            }
        }

        return '';
    }
}
