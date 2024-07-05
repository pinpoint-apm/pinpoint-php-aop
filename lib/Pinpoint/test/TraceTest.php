<?php
namespace Pinpoint\test;

use PHPUnit\Framework\TestCase;
use Pinpoint\Common\Logger;


class TraceTest extends TestCase
{
    public static $logger_;

    public static function setUpBeforeClass(): void
    {
        static::$logger_ = new TraceLogger();
        Logger::Inst()->setLogger(static::$logger_);
        Logger::Inst()->setLoggerLevel(0);
    }
    public static function tearDownAfterClass(): void
    {
        Logger::Inst()->setLogger(NULL);
        Logger::Inst()->setLoggerLevel(4);
    }

    protected function setUp(): void
    {
        static::$logger_->clear();
    }
    protected function tearDown(): void
    {
        static::$logger_->clear();
    }

    public function checkKeys(array $keys): bool
    {
        $message = static::$logger_->message_;
        foreach ($keys as $key => $time) {
            $this->assertEquals(substr_count($message, $key), $time);
        }
        return True;
    }
}