<?php

namespace pinpoint\test;
require_once 'bootstrap.php';
use pinpoint\Common\OriginFileVisitor;
use PHPUnit\Framework\TestCase;
use pinpoint\Common\PinpointDriver;
use pinpoint\Common\NamingConf;
use pinpoint\Common\RenderAopClass;

define('AOP_CACHE_DIR',__DIR__.'/Cache/');
define('PLUGINS_DIR',__DIR__.'/Plugins/');

/**
 * Class OrgClassParseTest
 * Test  convert user  class to dst AOP class
 * @package pinpoint\test
 */
class ClassParseTest extends TestCase
{
    public static  $naming=[];
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

// template for dst conf
//        $dst = [
//            'classCall'=>[
//                'PDO'=>'plugins\\PDO',
//                'type03'=>'plugins\\type03',
//                'test\\curl'=>'plugins\\test\\curl',
//                'test\\type1'=>'plugins\\test\\type1',
//                'app\\curl_init_01'=>'plugins\\app\\curl_init_01'
//            ],
//            'funCall'=>[
//                'curl_init'=>'plugins\\curl_init',
//                'app\\foo\\curl_02'=>'plugins\\app\\foo\\curl_02',
//                'app\\foo\\curl_03'=>'plugins\\app\\foo\\curl_0/cersv\3'
//            ],
//            'ignoreFiles'=>["pinpoint\\test\\IgnoreClass"],
//            'appendFiles'=>["pinpoint\\test\\AddClass"]
//        ];

        $nConf =new NamingConf(__DIR__.'/setting.ini');
        static::$naming = $nConf->getConf();
    }

    public function testTrait()
    {
        $fullpath =__DIR__.'/TestTrait.php';
        $info = [
            'getReturnType'=>[7,'pinpoint\\test','traitTestPlugin']
        ];
        // test empty naming
        $visitor = new OriginFileVisitor();

        $visitor->runAllVisitor($fullpath,$info);

        foreach ( RenderAopClass::getInstance()->getLoadeMap() as $class => $location)
        {
            $exp = str_replace('Cache','Comparison',$location);
            self::assertFileEquals($exp,$location);
        }
    }

    public function testClass()
    {
        $fullpath =__DIR__.'/TestClass.php';
        $info = [
            'foo'=>[7,'pinpoint\\test','traitTestPlugin'],
            'fooUseYield'=>[3,'pinpoint\\test','traitTestPlugin'],
            'fooNoReturn'=>[4,'pinpoint\\test','traitTestPlugin'],
            'fooNoReturnButReturn'=>[4,'pinpoint\\test\\burden\\depress\\herb\\e\\e\f\\longNp','victim'],
            'fooNaughtyFinal'=>[7,'\\','over'],
            '\PDO::query'=>[7,'pinpoint\\test','traitTestPlugin'],
            '\curl_exec' =>[7,'pinpoint\\test','traitTestPlugin'],
            'fooTestACPrivate' =>[4,'pinpoint\\test','traitTestPlugin'],
            'fooTestCompatible'=>[4,'pinpoint\\test','traitTestPlugin'],
            'returnNothing'=>[7,'pinpoint\\test','traitTestPlugin'],
            'returnNothing_1'=>[7,'pinpoint\\test','traitTestPlugin'],
            '__construct'=>[7,'pinpoint\\test','traitTestPlugin']
        ];


        $visitor =  new OriginFileVisitor();

        $visitor->runAllVisitor($fullpath,$info,static::$naming);
        foreach ( RenderAopClass::getInstance()->getLoadeMap()  as $class => $location)
        {
            $exp = str_replace('Cache','Comparison',$location);
            self::assertFileEquals($exp,$location);
        }

    }

    public function testNamingReplace()
    {
        $fullpath =__DIR__.'/Foo.php';
        $visitor =  new OriginFileVisitor();
        $visitor->runAllVisitor($fullpath,[],static::$naming);
        foreach ( RenderAopClass::getInstance()->getLoadeMap()  as $class => $location)
        {
            $exp = str_replace('Cache','Comparison',$location);
            self::assertFileEquals($exp,$location);
        }
    }

    public function testIgnoreList()
    {
//        $fullpath =__DIR__.'/Foo.php';
//        $osr = new OrgClassParse($fullpath,null,static::$naming);
//        foreach ($osr->classIndex as $class => $location)
//        {
//            $exp = str_replace('Cache','Comparison',$location);
//            self::assertFileEquals($exp,$location);
//        }
        self::assertTrue(true);
    }

}
