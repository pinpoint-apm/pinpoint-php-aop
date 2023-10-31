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

use Yii;
use Pinpoint\Common\Utils;
use Pinpoint\Plugins\PinpointPerRequestPlugins;
use Pinpoint\Common\JoinClassInterface;
use Pinpoint\Common\AspectClassHandle;
use Pinpoint\Common\Logger;

use Monolog\Logger as mLogger;
use Monolog\Handler\StreamHandler;


class Yii2PerRequestPlugins extends PinpointPerRequestPlugins implements JoinClassInterface
{
    public function __construct()
    {
        parent::__construct();
        // enable findFile patch
        Utils::addLoaderPatch(array($this, 'findFileInYii'), null);
        $log = new mLogger('Yii2PerRequestPlugins');
        $log->pushHandler(new StreamHandler('php://stdout', mLogger::DEBUG));
        Logger::Inst()->setLogger($log);
    }

    public function findFileInYii($className): string
    {
        if (isset(Yii::$classMap[$className])) {
            $classFile = Yii::$classMap[$className];
            if (strpos($classFile, '@') === 0) {
                return  Yii::getAlias($classFile);
            }
        } elseif (strpos($className, '\\') !== false) {
            $classFile = Yii::getAlias('@' . str_replace('\\', '/', $className) . '.php', false);
            if ($classFile === false || !is_file($classFile)) {
                return "";
            }
            return $classFile;
        }
        return "";
    }

    public function joinedClassSet(): array
    {
        $cls = [];
        $classHandler = new AspectClassHandle(\yii\web\UrlRule::class);
        $classHandler->addJoinPoint('parseRequest', yii2\UrlRule::class);

        $cls[] = $classHandler;
        return $cls;
    }
}
