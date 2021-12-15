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

namespace pinpoint\Common;


class PinpointDriver
{
    protected static $instance;
    protected $clAr;
    private $iniFile='';
    protected static $pluginDir = [PLUGINS_DIR."/AutoGen/",PLUGINS_DIR."/"]; //*Plugin.php
//    public function insertLoaderMap(string $name,string $path)
//    {
//        $this->classMap->insertMapping($name,$path);
//    }

//    //test only
//    public function getLoaderMap()
//    {
//        return $this->classMap->getLoadeMap();
//    }

    public static function getInstance(){

        if (!self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    final private function __construct()
    {
        $this->clAr = [];
//        $this->classMap = new RenderAopClass();
        $this->iniFile = PLUGINS_DIR."/setting.ini";
    }

    public static function getAutoGenPlugins()
    {
        $pluFiles = [];

        foreach (static::$pluginDir as $dir)
        {
            if(is_dir($dir))
            {
                Util::scanDir($dir,"/Plugin.php$/",$pluFiles);
                break;
            }
        }
        return $pluFiles;
    }

    public function start()
    {

        VendorAdaptorClassLoader::init();

        /// checking the cached file exist, if exist load it
        if(Util::checkCacheReady())
        {
            RenderAopClass::getInstance()->createFrom(Util::loadCachedClass());
            RenderAopClassLoader::start();
            return ;
        }

        $pluFiles = static::getAutoGenPlugins();
        foreach ($pluFiles as $file)
        {
            new PluginParser($file,$this->clAr);
        }
        /// there hidden a duplicated visit, class locates in @hook and appendFiles.
        /// while, it's safe to visit a class/file in appendfiles and @hook order
        $naming = [];
        if(file_exists($this->iniFile))
        {
            $nConf = new  NamingConf($this->iniFile);
            $naming = $nConf->getConf();

            if(isset($naming['appendFiles']))
            {
                foreach ($naming['appendFiles'] as $class)
                {
                    $fullPath = Util::findFile($class);
                    if(!$fullPath)
                        continue;
                    try
                    {
                        $visitor =  new OriginFileVisitor();
                        $visitor->runAllVisitor($fullPath,[],$naming);
                    }catch (\Exception $e){
                    }
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
//            try
//            {
            $visitor =  new OriginFileVisitor();
            if(isset($naming['ignoreFiles']) && in_array($class,$naming['ignoreFiles'])){
                $visitor->runAllVisitor($fullPath,$aopFuncInfo);
            }else{
                $visitor->runAllVisitor($fullPath,$aopFuncInfo,$naming);
            }
//            }catch (\Exception $e){
//
//            }
        }

        RenderAopClassLoader::start();
    }


}
