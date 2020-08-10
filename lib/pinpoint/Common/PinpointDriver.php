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

/**
 * User: eeliu
 * Date: 2/2/19
 * Time: 5:14 PM
 */

namespace pinpoint\Common;
use pinpoint\Common\OrgClassParse;
use pinpoint\Common\PinpointClassLoader;
use pinpoint\Common\AopClassMap;
use pinpoint\Common\PluginParser;

class PinpointDriver
{
    protected static $instance;
    protected $clAr;
    protected $classMap;

    /**
     * @return mixed
     */
    public function getClassMap()
    {
        return $this->classMap;
    }

    public static function getInstance(){

        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function __construct()
    {
        $this->clAr = [];
    }

    public function init(AopClassMap $classMap)
    {
        /// checking the cached file exist, if exist load it
        if(!$classMap->updateSelf())
        {
            PinpointClassLoader::init($classMap);
            return ;
        }

        $pluFiles = glob(PLUGINS_DIR."/*Plugin.php");
        $pluParsers = [];
        foreach ($pluFiles as $file)
        {
            $pluParsers[] = new PluginParser($file,$this->clAr);
        }

        foreach ($this->clAr as $cl=> $info)
        {
            if(empty($cl))
            {
                continue;
            }

            $fullPath = Util::findFile($cl);
            if(!$fullPath)
            {
                continue;
            }

            $osr = new OrgClassParse($fullPath,$cl,$info);
            foreach ($osr->classIndex as $clName=>$path)
            {
                $classMap->insertMapping($clName,$path);
            }
        }

        $classMap->persistenceClassMapping();

        PinpointClassLoader::init($classMap);

    }


}
