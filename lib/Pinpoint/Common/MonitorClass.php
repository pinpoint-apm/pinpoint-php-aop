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

class MonitorClass
{
    private static $_instance = null;

    private $classLoaderMap = [];
    // mark as private
    private function __construct()
    {
    }

    public function __destruct()
    {
    }

    public static function getInstance(): MonitorClass
    {
        if (self::$_instance) {
            return self::$_instance;
        }
        self::$_instance =  new MonitorClass();
        return self::$_instance;
    }

    public function createFrom(array $clsMap)
    {
        $this->classLoaderMap = $clsMap;
    }

    public function findFile(string $classFullName): string
    {
        Logger::Inst()->debug("try to loadclass '$classFullName'", [__CLASS__]);
        $classFile = $this->classLoaderMap[$classFullName];
        if (isset($classFile)) {
            return $classFile;
        }

        return '';
    }

    public  function insertMapping($cl, $file)
    {
        $this->classLoaderMap[$cl] = $file;
    }

    public function getJointClassMap(): array
    {
        return $this->classLoaderMap;
    }
}
