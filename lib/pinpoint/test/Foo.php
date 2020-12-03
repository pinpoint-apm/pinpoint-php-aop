<?php
namespace pinpoint\test;
use app\curl_init_01;
use \test\curl;
use \test\type1;
use type03;

use function app\foo\curl_02 as curl_02;
use function app\foo\curl_03;
use \PDO;
use \PDO as TPDO;
use A\B;
use App\A\LONG\NAME as ShortName;
use A\B\C;


class Foo
{
    public function __construct($str,type1 $one,\test\type2 $two, type03 $three)
    {
        $table = new type03();
        $curl = \curl_init();
        $curl_01 = new curl_init_01();
        $curl_02 = new \app\curl_init_01();
        $a = new ShortName\ClassA();
        print_r(__CLASS__);
        print_r(__LINE__);
        print_r(__FUNCTION__);
        print_r(23);
    }

    public function  testPDO()
    {
        $pdo = new \PDO('xx','xx','xx');
        $pdo2 = new PDO('xx','xx','xx');
        $np = new B\LongNP();
        $abcd= new C\classC();
        return [$pdo,$pdo2,$np];
    }

    public function __destruct()
    {

    }

    public function returnPDO()
    {
        $pdoClass=\PDO::class;
        return TPDO::class;
    }
}