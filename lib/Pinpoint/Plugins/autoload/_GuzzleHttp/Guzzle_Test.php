<?php
namespace Pinpoint\Plugins\autoload\_GuzzleHttp;

use Pinpoint\test\TraceTest;


class Guzzle_Test extends TraceTest
{
    public function test_sync()
    {
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', 'http://httpbin.org/get');

        $this->assertEquals($response->getStatusCode(), 200);
        $response_ar = json_decode($response->getBody()->getContents(), true);
        var_dump($response_ar);
        $this->assertArrayHasKey('Pinpoint-Flags', $response_ar['headers']);
        $this->assertArrayHasKey('Pinpoint-Papptype', $response_ar['headers']);
        $this->assertArrayHasKey('Pinpoint-Pspanid', $response_ar['headers']);
        $this->assertArrayHasKey('Pinpoint-Sampled', $response_ar['headers']);
        $this->assertArrayHasKey('Pinpoint-Traceid', $response_ar['headers']);
        echo static::$logger_->message_;
    }

}