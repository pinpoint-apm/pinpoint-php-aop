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

class StanderClassLoader
{
    protected $findClassFunc = [];
    protected $loadClassFunc = [];
    public function __construct(array &$orgLoader)
    {
        Logger::Inst()->debug(" create a StanderClassLoader");
        $this->loadClassFunc =  $orgLoader;
        if ($orgLoader[0] instanceof ClassLoader) {
            $this->findClassFunc = [$orgLoader[0], 'findFile'];
        }
    }

    public function getClassLoader(): callable
    {
        return [$this, 'loadClass'];
    }

    public function findFile(string $classFullName): string
    {
        Logger::Inst()->debug("findClass:'$classFullName'");
        if (is_callable($this->findClassFunc)) {
            $file = call_user_func($this->findClassFunc, $classFullName);
            if ($file !== false) {
                Logger::Inst()->debug("findClass:'$classFullName' ->'$file'");
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
        Logger::Inst()->debug("try to loadClass:'$class'", ['StanderClassLoader']);
        if (is_callable($this->loadClassFunc)) {
            $classFilePath = call_user_func($this->loadClassFunc, $class);
            if (is_string($classFilePath) && file_exists($classFilePath)) {
                Logger::Inst()->debug("loadClass:'$class' -> '$classFilePath'", ['StanderClassLoader']);
                require_once $classFilePath;
            }
            return $classFilePath;
        }
    }
}
