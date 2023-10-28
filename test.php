<?php

declare(strict_types=1);

namespace a\ba\c;

class X
{
    function out()
    {
    }
}

class X1 extends X
{
}

// class_alias('X', 'Y');
// class_alias('Y', 'Z');
$x = new X1();

function test(X $x)
{
    $z = new \ReflectionClass($x);
    echo $z->getName(); // X
}

// test(new X1());



class A
{
    public int $a = 10;
    public int $b = 10;
}


$a = new A();

print_r($a);
$a->a = 12;
print_r($a);

function change_a(A $a)
{
    $a->a = 13;
}

function change_a1(A $a)
{
    $a->a = 14;
}

change_a($a);
print_r($a);

change_a1($a);
print_r($a);


$test_ar = array(
    "a" => 1,
    "b" => 3
);


print_r(array_key_exists('b', $test_ar));

print(A::class);
