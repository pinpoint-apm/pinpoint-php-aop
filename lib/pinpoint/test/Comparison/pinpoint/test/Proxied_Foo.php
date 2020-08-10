<?php

namespace pinpoint\test;

use plugins\app\curl_init_01;
use plugins\test\curl;
use plugins\test\type1;
use plugins\type03;
use function plugins\app\foo\curl_02 as curl_02;
use function plugins\app\foo\curl_03;
class Proxied_Foo
{
    public function __construct($str, type1 $one, \test\type2 $two, type03 $three)
    {
        $table = new type03();
        $curl = \curl_init();
        $curl_01 = new curl_init_01();
        $curl_02 = new \app\curl_init_01();
        print_r('pinpoint\\test\\Foo');
        print_r(21);
        print_r('pinpoint\\test\\Foo::__construct');
        print_r(23);
    }
    public function testPDO()
    {
        $pdo = new PDO('xx', 'xx', 'xx');
    }
    public function __destruct()
    {
    }
}