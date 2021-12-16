<?php declare(strict_types=1);
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
    private $settingIni=PLUGINS_DIR."/setting.ini";
    // user autoGen class and internal autoGen class
    private static $autoGenDirs = [PLUGINS_DIR."/AutoGen/" , __DIR__."/../Plugins/AutoGen"]; //*Plugin.php

    public static function getInstance(){

        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    final private function __construct()
    {
    }

    public static function getAutoGenFiles()
    {
        $files = [];

        foreach (static::$autoGenDirs as $dir)
        {
            if(is_dir($dir))
            {
                Util::scanDir($dir,"/Plugin.php$/",$files);
            }
        }

        return $files;
    }

    public function start()
    {

        RenderAopClass::getInstance();
        /// checking the cached file exist, if exist load it
        if(Util::checkCacheReady())
        {
            RenderAopClass::getInstance()->createFrom(Util::loadCachedClass());
            RenderAopClassLoader::start();
            return ;
        }
        VendorAdaptorClassLoader::enable();
        $pluFiles = static::getAutoGenFiles();
        foreach ($pluFiles as $file)
        {
            new PluginParser($file,$this->clAr);
        }
        /// there hidden a duplicated visit, class locates in @hook and appendFiles.
        /// while, it's safe to visit a class/file in appendfiles and @hook order
        $naming = [];
        if(file_exists($this->settingIni))
        {
            $nConf = new NamingConf($this->settingIni);
            $naming = $nConf->getConf();

            if(isset($naming['appendFiles']))
            {
                foreach ($naming['appendFiles'] as $class)
                {
                    $fullPath = Util::findFile($class);
                    if(!$fullPath)
                        continue;
                    $visitor =  new OriginFileVisitor();
                    $visitor->runAllVisitor($fullPath,[],$naming);
                }
            }
        }


        foreach ($this->clAr as $class => $aopFuncInfo)
        {
            if(empty($class))
                continue;
            $fullPath = Util::findFile($class);
            if(!$fullPath )
                continue;
            $visitor =  new OriginFileVisitor();
            if(isset($naming['ignoreFiles']) && in_array($class,$naming['ignoreFiles'])){
                $visitor->runAllVisitor($fullPath,$aopFuncInfo);
            }else{
                $visitor->runAllVisitor($fullPath,$aopFuncInfo,$naming);
            }
        }
        // save render aop class into index file
        Util::saveCachedClass(RenderAopClass::getInstance()->getLoadeMap());
        // enable RenderAop class loader
        RenderAopClassLoader::start();
    }


}
