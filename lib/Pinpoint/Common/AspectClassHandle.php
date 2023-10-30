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


class AspectClassHandle
{
    public string $name;
    public $methodJoinPoints = array();
    public $classAlias = array();
    public $funcAlias = array();

    public function __construct(string $_name)
    {
        $this->name = $_name;
    }

    public function getMethodJoinPoints()
    {
        return $this->methodJoinPoints;
    }

    public function addJoinPoint(string $method, string $monitor)
    {
        if (method_exists($this->name, $method) == false) {
            throw new \Exception("no '$method' in '$this->name'");
        }

        $this->methodJoinPoints[$method] = $monitor;
    }

    public function addClassNameAlias(string $olderName, string $newName)
    {
        $this->classAlias[$olderName] = $newName;
    }

    public function addFunctionAlias(string $olderName, string $newName)
    {
        $this->funcAlias[$olderName] = $newName;
    }
}
