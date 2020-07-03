<?php


namespace pinpoint\test;
use App\Class1;
use App\Class2;
use App\Class3 as FooClass,App\Class4 as FooClass4;

abstract class TestClass
{
    public  function __construct($a,$b,$c)
    {

    }

    public function foo($a,$b,$v,$d) :array
    {
        echo date("y-m-d");
        return [$a,$b,$v,$d];
    }

    public function fooUseYield()
    {
        $i = 1000;
        yield $i +1;
        yield $i +2;
        yield $i +3;
    }

    public function fooNoReturn()
    {
        $i = 1000;
        throw new \Exception("I just want to throw sth");
    }

    public function fooNoReturnButReturn()
    {
        $i = 1000;
        throw new \Exception("I just want to throw sth");
        return "hello black hole";
    }

    public final function fooNaughtyFinal($a,$b,$c)
    {
        yield $a;
        yield $b;
        yield $c;
    }

    public function fooTestBi()
    {
        $ch = \curl_init();
        \curl_exec($ch);
        curl_close();
        $username = '2343';
        $passwd = "152351";
        $mysql = new \PDO("mysql:host=localhost;dbname=user", $username , $passwd);
        $mysql->query('SELECT name, color, calories FROM fruit ORDER BY name');

    }

    private function fooTestACPrivate()
    {
        echo "I'm a private function";
        return "OK";
    }
    public function fooTestCompatible(class1 $a,class2 $b,FooClass $c,FooClass4 $d)
    {
        return null;
    }

    public  function returnNothing(): void
    {
        return ;
    }
    public  function returnNothing_1()
    {
        return ;
    }
}
