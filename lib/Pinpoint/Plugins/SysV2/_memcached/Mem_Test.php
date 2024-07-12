<?php

namespace Pinpoint\Plugins\SysV2\_memcached;

use Pinpoint\test\TraceTest;

use Memcached;

// vendor/bin/phpunit --configuration PHPUnit_aop_libraries.xml --testsuit pinpoint --testdox --filter Mem_Test
class Mem_Test extends TraceTest
{

    public function test_mem_flow()
    {
        $this->assertTrue(extension_loaded('memcached'));
        $mc = new Memcached();
        $mc->addServer("localhost", 11211);
        $mc->set('key', "abc");
        $this->assertTrue($mc->get('key') == 'abc');       // boolean false
        var_dump($mc->getResultCode());  // int 0 which is Memcached::RES_SUCCESS
        var_dump($mc->add("test_add", 234));  // int 0 which is Memcached::RES_SUCCESS
        // var_dump($Memcached->appendByKey("xxx", "test_add", 234));  // int 0 which is Memcached::RES_SUCCESS
        var_dump($mc->delete("test_add"));  // int 0 which is Memcached::RES_SUCCESS
        var_dump($mc->deleteMulti(["test_add", "a", "b", "c"]));  // int 0 which is Memcached::RES_SUCCESS
    }
}