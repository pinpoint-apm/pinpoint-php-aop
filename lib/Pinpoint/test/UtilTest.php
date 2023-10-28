<?php
/**
 * User: eeliu
 * Date: 2/1/19
 * Time: 4:27 PM
 */

namespace Pinpoint\test;
require_once 'bootstrap.php';

use PHPUnit\Framework\TestCase;
use Pinpoint\Common\Utils;
use Pinpoint\Common\ClassFile;
use Pinpoint\Common\VendorAdaptorClassLoader;
//use app\Foo;

class UtilTest extends TestCase
{
    public function setUp()
    {
        VendorAdaptorClassLoader::enable();
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function testFindFile()
    {

        self::assertEquals(__FILE__,Utils::findFile(UtilTest::class));
        self::assertStringEndsWith('php', Utils::findFile(ClassFile::class));
        self::assertTrue(Utils::findFile("xx\Foo") == '');
    }

    public function testparseUserFunc()
    {
        $strs = ['///@hook:\app\Foo::foo_p2 \app\Foo::foo_p1',
            '//@hook:\app\Foo::foo_p2 \app\Foo::foo_p1',
            '* @hook:\app\Foo::foo_p2 \app\Foo::foo_p1 '];
        foreach ($strs as $str)
        {
            $ret =Utils::parseUserFunc($str);
            var_dump($ret);
            self::assertEquals(serialize($ret),serialize(['\app\Foo::foo_p2','\app\Foo::foo_p1']));
        }

        self::assertEquals(serialize(Utils::parseUserFunc($str)),serialize(['\app\Foo::foo_p2','\app\Foo::foo_p1']));

        self::assertEquals(count(Utils::parseUserFunc('\app\Foo::foo_p2 \app\Foo::foo_p1')),0);
        self::assertEquals(count(Utils::parseUserFunc('')),0);
        self::assertEquals(count(Utils::parseUserFunc('I don\'t now nothing')),0);
    }

    public function testSan()
    {
        $tree = [];
        Utils::scanDir('.',"/s.php$/",$tree);
        self::assertNotEmpty($tree);
    }
}