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
 * Time: 3:04 PM
 */

namespace pinpoint\test;

require_once 'bootstrap.php';

use PHPUnit\Framework\TestCase;
use pinpoint\test\TestTrait;
use pinpoint\Common\PluginParser;

use pinpoint\Plugins\CommonPlugin;
use pinpoint\Common\Util;

/**
 * Test findFile
 * Class PluginParserTest
 * @package pinpoint\test
 */
class PluginParserTest extends TestCase
{
    public function testRun()
    {
        self::assertFalse(Util::findFile(CommonPlugin::class));
        self::assertNotEmpty(Util::findFile(PluginParser::class));
        self::assertNotEmpty(Util::findFile(TestTrait::class));

//        $clAr = array();
//        $var = new PluginParser(Util::findFile('Pinpoint\Plugins\CommonPlugin'),$clAr);
//        $var->run();
//        self::assertEquals($var->getClassName(), "CommonPlugin");
//        self::assertEquals($var->getNamespace(), 'Pinpoint\Plugins');
//        $array = $var->getClArray();
//        self::assertArrayHasKey("app\Foo::foo_p1",$array);
//        self::assertArrayHasKey("app\Foo::print_r",$array);
//        self::assertArrayHasKey("app\Foo::curl_init",$array);
//        self::assertArrayNotHasKey("test",$array);
//        self::assertArrayNotHasKey("format",$array);
//        self::assertArrayHasKey("app\Foo::curl_setopt",$array);
//        self::assertEquals($array['app\Foo::foo_p1']['mode'] ,7);
//        self::assertEquals($array['app\Foo::print_r']['mode'] ,1);
//        self::assertEquals($array['app\Foo::curl_init']['mode'] ,7);

    }
}
