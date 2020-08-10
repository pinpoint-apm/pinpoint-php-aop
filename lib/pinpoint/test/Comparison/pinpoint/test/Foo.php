<?php

namespace pinpoint\test;

use plugins\app\curl_init_01;
use plugins\test\curl;
use plugins\test\type1;
use plugins\type03;
use plugins\app\foo\curl_02 as curl_02;
use plugins\app\foo\curl_03;
use pinpoint\test\Proxied_Foo;
class Foo extends Proxied_Foo
{
}