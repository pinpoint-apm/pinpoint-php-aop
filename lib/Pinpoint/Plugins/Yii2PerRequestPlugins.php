<?php

/******************************************************************************
 * Copyright 2020 NAVER Corp.                                                 *
 *                                                                            *
 * Licensed under the Apache License, Version 2.0 (the "License");            *
 * you may not use this file except in compliance with the License.           *
 * You may obtain a copy of the License at                                    *
 *                                                                            *
 *     http://www.apache.org/licenses/LICENSE-2.0                             *
 *                                                                            *
 * Unless required by applicable law or agreed to in writing, software        *
 * distributed under the License is distributed on an "AS IS" BASIS,          *
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.   *
 * See the License for the specific language governing permissions and        *
 * limitations under the License.                                             *
 ******************************************************************************/

namespace Pinpoint\Plugins;

use yii;
use Pinpoint\Plugins\PinpointPerRequestPlugins;
use Pinpoint\Common\UserFrameworkInterface;
use Pinpoint\Common\AspectClassHandle;
use Pinpoint\Common\Logger;

use Monolog\Logger as mLogger;
use Monolog\Handler\StreamHandler;


class Yii2PerRequestPlugins extends PinpointPerRequestPlugins implements UserFrameworkInterface
{
    // private $_yiiLoader = array();
    public function __construct()
    {
        parent::__construct();
        // enable findFile patch
        $log = new mLogger('yii2');
        $log->pushHandler(new StreamHandler('php://stdout', mLogger::INFO));
        Logger::Inst()->setLogger($log);
    }
    /**
     * port from https://github.com/yiisoft/yii2/blob/6804fbeae8aa5f8ad5066b50f1864eb0b9d77849/framework/BaseYii.php#L279-L293
     */
    public function findFileInYii2($className): string
    {
        $classFile = false;
        Logger::Inst()->debug("try to yii::loader '$className' ");
        if (isset(Yii::$classMap[$className])) {
            $classFile = Yii::$classMap[$className];
            if (strpos($classFile, '@') === 0) {
                $classFile = Yii::getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
            $classFile = Yii::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
        }

        if ($classFile === false || !is_file($classFile)) {
            return "";
        }
        Logger::Inst()->debug("yii::loader '$className' ->'$classFile'");
        // require_once file
        return $classFile;
    }

    public function userFindClass(&$loader): callable
    {
        return [$this, 'findFileInYii2'];
    }

    public function joinedClassSet(): array
    {
        $cls = [];
        // \yii\web\UrlRule
        $classHandler = new AspectClassHandle(\yii\web\UrlRule::class);
        $classHandler->addJoinPoint('parseRequest', \Pinpoint\Plugins\yii2\UrlRule::class);
        $cls[] = $classHandler;

        // yii\db\Connection::createPdoInstance
        $classHandler = new AspectClassHandle(\yii\db\Connection::class);
        $classHandler->addJoinPoint('createPdoInstance', \Pinpoint\Plugins\yii2\ConnectionPlugin::class);
        $cls[] = $classHandler;
        return $cls;
    }
}
