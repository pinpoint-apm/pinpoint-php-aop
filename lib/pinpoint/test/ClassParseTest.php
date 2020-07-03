<?php

namespace pinpoint\test;
require_once 'bootstrap.php';
use pinpoint\Common\OrgClassParse;
use PHPUnit\Framework\TestCase;

define('AOP_CACHE_DIR',__DIR__.'/Cache/');
define('PLUGINS_DIR',__DIR__.'/Plugins/');

/**
 * Class OrgClassParseTest
 * Test  convert user  class to dst AOP class
 * @package pinpoint\test
 */
class ClassParseTest extends TestCase
{
    public static function cleanDir($dirName)
    {
        $files = glob($dirName."*",GLOB_MARK);
        foreach ($files as $obj)
        {
            if(!is_dir($obj))
            {
                unlink($obj);
            }else{
                static::cleanDir($obj);
            }
        }
    }

    public static function setUpBeforeClass()
    {
        static::cleanDir(AOP_CACHE_DIR);
    }

    public function testTrait()
    {
        $fullpath =__DIR__.'/TestTrait.php';
        $cl = "pinpoint\\test\\TestTrait";
        $info = [
            'getReturnType'=>[7,'pinpoint\\test','traitTestPlugin']
        ];

        $osr = new OrgClassParse($fullpath,$cl,$info);

        foreach ($osr->classIndex as $class => $location)
        {
            $exp = str_replace('Cache','Comparison',$location);
            self::assertFileEquals($exp,$location);
        }

        $requireFile = $osr->requiredFile;
        $expRequired= str_replace('Cache','Comparison',$requireFile);
        self::assertFileEquals($requireFile,$expRequired);
    }

    public function testClass()
    {
        $fullpath =__DIR__.'/TestClass.php';
        $cl = "pinpoint\\test\\TestClass";
        $info = [
            'foo'=>[7,'pinpoint\\test','traitTestPlugin'],
            'fooUseYield'=>[3,'pinpoint\\test','traitTestPlugin'],
            'fooNoReturn'=>[4,'pinpoint\\test','traitTestPlugin'],
            'fooNoReturnButReturn'=>[4,'pinpoint\\test\\burden\\depress\\herb\\e\\e\f\\longNp','victim'],
            'fooNaughtyFinal'=>[7,'','over'],
            '\PDO::query'=>[7,'pinpoint\\test','traitTestPlugin'],
            '\curl_exec' =>[7,'pinpoint\\test','traitTestPlugin'],
            'fooTestACPrivate' =>[4,'pinpoint\\test','traitTestPlugin'],
            'fooTestCompatible'=>[4,'pinpoint\\test','traitTestPlugin'],
            'returnNothing'=>[7,'pinpoint\\test','traitTestPlugin'],
            'returnNothing_1'=>[7,'pinpoint\\test','traitTestPlugin'],
            '__construct'=>[7,'pinpoint\\test','traitTestPlugin']
        ];


        $osr = new OrgClassParse($fullpath,$cl,$info);

        foreach ($osr->classIndex as $class => $location)
        {
            $exp = str_replace('Cache','Comparison',$location);
            self::assertFileEquals($exp,$location);
        }

        $requireFile = $osr->requiredFile;
        $expRequired= str_replace('Cache','Comparison',$requireFile);
        self::assertFileEquals($requireFile,$expRequired);
    }
}
