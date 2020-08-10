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

use Composer\Autoload\ClassLoader;
use pinpoint\Common\AopClassMap;
class PinpointClassLoader
{
    public  static $inInitalized;
    private $origin; //  origin classloader
    private $classMap;
    public function __construct($origin, AopClassMap $classMap)
    {
        $this->classMap = $classMap;

        $this->origin = $origin;
        assert($origin);
    }

    public function findFile(string $classFullName)
    {
        $file = $this->classMap->findFile($classFullName);

        if( ! $file )
        {
            $file = $this->origin->findFile($classFullName);
            if ($file !== false)
            {
                $file = realpath($file) ?: $file;
            }
        }
        return $file;
    }

    public function loadClass($class)
    {
        $file = $this->findFile($class);

        if ($file !== false) {
            include $file;
        }
    }

    /**
     * register pinpoint aop class loader, wrapped vendor class loader
     * @param $classIndex
     * @return bool
     */
    public static  function init($classIndex)
    {
        $loaders = spl_autoload_functions();
        foreach ($loaders as &$loader) {
            $loaderToUnregister = $loader;
            if (is_array($loader) && ($loader[0] instanceof ClassLoader)) {
                // unregister composer loader
                spl_autoload_unregister($loaderToUnregister);
                // $originalLoader = $loader[0];
                // replace composer loader with aopclassLoader
                $loader[0] = new PinpointClassLoader($loader[0],$classIndex);
                spl_autoload_register($loader,true,true);
                self::$inInitalized = true;
            }
        }

        return self::$inInitalized;

    }

}
