<?php

namespace pinpoint\test;

use plugins\app\curl_init_01;
use plugins\test\curl;
use plugins\test\type1;
use plugins\type03;
use function plugins\app\foo\curl_02 as curl_02;
use function plugins\app\foo\curl_03;
use plugins\PDO;
use plugins\PDO as TPDO;
use plugins\A\B;
use App\A\LONG\NAME as ShortName;
use A\B\C;
class Foo
{
    public function __construct($str, type1 $one, \test\type2 $two, type03 $three)
    {
        $table = new type03();
        $curl = \plugins\curl_init();
        $curl_01 = new curl_init_01();
        $curl_02 = new \plugins\app\curl_init_01();
        $a = new \plugins\app\ClassA();
        print_r('pinpoint\\test\\Foo');
        print_r(27);
        print_r('pinpoint\\test\\Foo::__construct');
        print_r(23);
    }
    public function testPDO()
    {
        $pdo = new \plugins\PDO('xx', 'xx', 'xx');
        $pdo2 = new PDO('xx', 'xx', 'xx');
        $np = new B\LongNP();
        $abcd = new \plugins\C();
        return [$pdo, $pdo2, $np];
    }
    public function __destruct()
    {
    }
    public function returnPDO()
    {
        $pdoClass = \plugins\PDO::class;
        return TPDO::class;
    }
}