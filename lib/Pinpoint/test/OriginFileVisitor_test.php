<?php

namespace Pinpoint\test;

require_once 'bootstrap.php';

use PHPUnit\Framework\TestCase;
use Pinpoint\Common\OriginFileVisitor;
use Pinpoint\Common\Utils;
use Pinpoint\Common\JoinClass;
use Pinpoint\Common\VendorAdaptorClassLoader;
use Pinpoint\Common\RenderAopClass;

define('CLASS_PREFIX', 'Proxy');
define('AOP_CACHE_DIR', __DIR__ . '/Cache');

/**
 * Class OrgClassParseTest
 * Test  convert user  class to dst AOP class
 * @package pinpoint\test
 */
class OriginFileVisitor_test extends TestCase
{
    public function setUp()
    {
        VendorAdaptorClassLoader::enable();
        parent::setUp();
    }

    public function test_FileVisitor()
    {
        $fullPath = Utils::findFile(Bear::class);
        $this->assertFileExists($fullPath);
        $joinClass = new JoinClass(Bear::class);
        $joinClass->addJoinPoint('output', OutputMonitor::class);
        $joinClass->addJoinPoint('noreturn', OutputMonitor::class);
        $visitor = new OriginFileVisitor();
        $visitor->runAllVisitor($fullPath, $joinClass);
        $classMap = RenderAopClass::getInstance()->getJointClassMap();
        $this->assertArrayHasKey(Bear::class,$classMap);
        foreach ($classMap as $class => $file){
            // print_r(str_replace("Comparison","Cache",$file));
            $this->assertFileEquals($file,str_replace("Cache","Comparison",$file));
        }
        // $this->assertFileEquals("");
    }
}

