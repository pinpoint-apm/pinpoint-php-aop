<?php
namespace Pinpoint\Common;

use PHPUnit\Framework\TestCase;


class GenClassIndexMap_Test extends TestCase
{
    public function test_save()
    {

        $ar = [
            'a' => 'b',
            'a1' => 'b3',
            'a2' => 'b4',
        ];
        $gcls = new GenClassIndexMap($ar);
        $path = sys_get_temp_dir() . '/test.php';
        $gcls->save($path);
        $new_ar = include $path;
        $this->assertEquals($ar, $new_ar);
    }
}