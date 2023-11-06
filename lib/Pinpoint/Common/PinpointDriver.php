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


class PinpointDriver
{
    protected static $instance;
    protected $clAr = [];

    private UserFrameworkInterface $reqInst;

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
            assert(is_a($this->reqInst, 'Pinpoint\Common\UserFrameworkInterface'));
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
        if (Utils::checkCacheReady()) {
            Logger::Inst()->debug("found cache");
            MonitorClass::getInstance()->createFrom(Utils::loadCachedClass());
            MonitorClassLoader::start();
            return;
        }
        Logger::Inst()->debug("no found cache, try to generate joinclass");
        VendorClassLoaderAdaptor::Inst()->setUserFindClass($this->reqInst);
        VendorClassLoaderAdaptor::Inst()->start();

        $joinedClassSet = $this->reqInst->joinedClassSet();
        if (empty($joinedClassSet)) {
            return;
        }

        foreach ($joinedClassSet as $aspClassHandler) {
            assert(is_a($aspClassHandler, '\Pinpoint\Common\AspectClassHandle'));
            $fullClassName = $aspClassHandler->aspClassName;
            if (empty($fullClassName)) {
                continue;
            }
            $fullPath = VendorClassLoaderAdaptor::Inst()->findFileViaSpl($fullClassName);
            Logger::Inst()->debug("found aspectClass '$fullClassName' -> '$fullPath' ");
            // Please DO NOT CHEAT ME
            assert(file_exists($fullPath), "'$fullClassName' ->'$fullPath' must exist");

            $visitor = new OriginFileVisitor();
            $visitor->runAllVisitor($fullPath, $aspClassHandler);
        }
        // save render aop class into index file
        Utils::saveCachedClass(MonitorClass::getInstance()->getJointClassMap());
    }
}
