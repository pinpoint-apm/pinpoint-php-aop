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

/**
 * User: eeliu
 * Date: 2/2/19
 * Time: 5:14 PM
 */

namespace Pinpoint\Common;

use Exception;
use Pinpoint\Plugins\PerRequestPlugins;

class PinpointDriver
{
    protected static $instance;
    protected $clAr = [];

    private I_PerRequest $reqInst;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    final private function __construct()
    {
        if (defined('PP_REQ_PLUGINS')  && class_exists(PP_REQ_PLUGINS)) {
            $userPerRequestClass = PP_REQ_PLUGINS;
            $this->reqInst = new $userPerRequestClass();
            assertInstanceOf('I_PerRequest', $this->reqInst);
        } else {
            $this->reqInst = new PerRequestDefault();
        }
    }

    public function getRequestInst()
    {
        return $this->reqInst;
    }

    public function start()
    {
        $joinedClass = $this->reqInst->joinedClass();
        if (empty($joinedClass)) {
            return;
        }

        RenderAopClass::getInstance();
        /// checking the cached file exist, if exist load it
        if (Utils::checkCacheReady()) {
            RenderAopClass::getInstance()->createFrom(Utils::loadCachedClass());
            RenderAopClassLoader::start();
            return;
        }
        VendorAdaptorClassLoader::enable();
        foreach ($joinedClass as $fullClassName => $junction) {
            if (empty($fullClassName))
                continue;
            $fullPath = Utils::findFile($fullClassName);
            // Please DO NOT CHEAT ME
            assertFileExists($fullPath, "'$fullPath' must exist");
            assertInstanceOf("\Pinpoint\Common\Pinpoint\JoinClass", $junction);
            
            $visitor = new OriginFileVisitor();
            $visitor->runAllVisitor($fullPath, $junction);
        }
        // save render aop class into index file
        Utils::saveCachedClass(RenderAopClass::getInstance()->getJointClassMap());
        // enable RenderAop class loader
        RenderAopClassLoader::start();
    }

    /**
     * start /tail are the spl_autoload_functions checking order
     * @param callable $start
     * @param callable $tail
     */
    public function addClassFinderHelper(callable $start, callable $tail)
    {
        Utils::addLoaderPatch($start, $tail);
    }
}
