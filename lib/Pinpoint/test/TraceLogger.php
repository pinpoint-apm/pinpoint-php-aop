<?php
namespace Pinpoint\test;


class TraceLogger
{
    public $message_;
    public function debug($message, array $context = [])
    {
        $this->message_ .= $message . "\n";
    }
    public function warning($message, array $context = [])
    {
    }
    public function info($message, array $context = [])
    {
    }

    public function clear()
    {
        $this->message_ = '';
    }
}
