<?php

namespace Pinpoint\test;

require_once 'bootstrap.php';

use PHPUnit\Framework\TestCase;
use Pinpoint\Common\OriginFileVisitor;
use Pinpoint\Common\Utils;
use Pinpoint\Common\AspectClassHandle;
use Pinpoint\Common\VendorClassLoaderAdaptor;
use Pinpoint\Common\MonitorClass;

define('CLASS_PREFIX', 'Proxy');
define('AOP_CACHE_DIR', __DIR__ . '/Cache');

/**
 * Class OrgClassParseTest
 * Test  convert user  class to dst AOP class
 * @package pinpoint\test
 */
class TestOriginFileVisitor extends TestCase
{
    public function setUp()
    {
        VendorClassLoaderAdaptor::Inst()->start();
        parent::setUp();
    }

    public function test_FileVisitor()
    {
        $fullPath = VendorClassLoaderAdaptor::Inst()->findFileViaSpl(Bear::class);
        $this->assertFileExists($fullPath);
        $classHandler = new AspectClassHandle(Bear::class);
        $classHandler->addJoinPoint('output', OutputMonitor::class);
        $classHandler->addJoinPoint('noreturn', OutputMonitorNoReturn::class);

        $classHandler->addClassNameAlias('PDO', \Pinpoint\Plugins\Sys\PDO\PDO::class);
        $classHandler->addFunctionAlias('curl_init', 'Pinpoint\Plugins\Sys\curl\curl_init');
        $classHandler->addFunctionAlias('curl_setopt', 'Pinpoint\Plugins\Sys\curl\curl_setopt');
        $classHandler->addFunctionAlias('curl_exec', 'Pinpoint\Plugins\Sys\curl\curl_exec');
        $classHandler->addFunctionAlias('curl_close', 'Pinpoint\Plugins\Sys\curl\curl_close');

        $classHandler->addJoinPoint('checkProtected', OutputMonitor::class);
        $classHandler->addJoinPoint('checkPrivate', OutputMonitor::class);

        $visitor = new OriginFileVisitor();
        $visitor->runAllVisitor($fullPath, $classHandler);
        $classMap = MonitorClass::getInstance()->getJointClassMap();
        $this->assertArrayHasKey(Bear::class, $classMap);
        foreach ($classMap as $class => $file) {
            // print_r(str_replace("Comparison","Cache",$file));
            $this->assertFileEquals($file, str_replace("Cache", "Comparison", $file));
        }
        Utils::saveCachedClass(MonitorClass::getInstance()->getJointClassMap());
    }
}
