<?php

namespace Pinpoint\test;

class ProxyBear
{
    public function output(string $_1, int $_2, array &$_3)
    {
        return 1010;
    }
    public function noreturn(string $_1, int $_2, array &$_3, $a, $b, $c)
    {
    }
}