<?php
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
