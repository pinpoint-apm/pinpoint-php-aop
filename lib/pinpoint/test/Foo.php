<?php

namespace app\test;
use \curl_init;
use app\curl_init_01;
use \test\curl;
use \test\type1;
use type03;

use function app\foo\curl_02 as curl_02;
use function app\foo\curl_03;


class Foo
{
    public function __construct(String $str,type1 $one,\test\type2 $two, type03 $three)
    {

    }

    public function __destruct()
    {

    }
}


