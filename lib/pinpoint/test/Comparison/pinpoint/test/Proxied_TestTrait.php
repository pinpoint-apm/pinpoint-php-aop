<?php


namespace pinpoint\test;

trait Proxied_TestTrait
{
    function getReturnType()
    {
        echo "1";
    }

    function getReturnDescription()
    {
        echo "2" ;

    }

}require AOP_CACHE_DIR . 'pinpoint/test/TestTrait_required.php';
