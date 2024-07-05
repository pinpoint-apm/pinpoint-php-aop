<?php
namespace Pinpoint\Plugins\autoload\_predis;

use Pinpoint\test\TraceTest;


class pRedis_Test extends TraceTest
{
    public function test_ar_pare()
    {
        $client = new \Predis\Client([
            'scheme' => 'tcp',
            'host' => 'localhost',
            'port' => 6379,
        ]);
        $client->set('foo', 'bar');
        $client->get('foo');
        $this->checkKeys([
            "Predis::set" => 2,
            "Predis::get" => 2,
        ]);
    }

    public function test_url()
    {
        $client = new \Predis\Client("tcp://localhost:6379");
        $client->set('foo', 'bar');
        $client->get('foo');
        $this->checkKeys([
            "Predis::set" => 2,
            "Predis::get" => 2,
        ]);
    }
}