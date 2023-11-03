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

class ClassLoaderFactory
{
    private $_userFindClass = null;
    public function __construct()
    {
    }

    public function setUserFindClass(UserFrameworkInterface $userFindClass)
    {
        $this->_userFindClass = $userFindClass;
    }

    public function createLoader(&$originLoader): StanderClassLoader
    {
        $loaderClass = $originLoader[0];

        if (is_a($loaderClass, ClassLoader::class))
            return new StanderClassLoader($originLoader);

        return new UserFindClassLoader($originLoader, $this->_userFindClass);
    }
}
