<?php

declare(strict_types=1);
/**
 * Copyright 2020-present NAVER Corp.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Pinpoint\Common;

class Logger
{
    private $log = null;
    private static $_inst;
    private function __construct()
    {
    }

    private function defaultLogger(string $message, $context)
    {
        $ctx = implode("", $context);
        error_log($message . " " . "'$ctx'", 0);
    }

    public static function Inst()
    {
        if (!(self::$_inst instanceof self)) {
            self::$_inst = new self;
        }
        return self::$_inst;
    }

    public function setLogger($logger)
    {
        assert(method_exists($logger, 'debug'));
        assert(method_exists($logger, 'warning'));
        assert(method_exists($logger, 'info'));
        $this->log = $logger;
    }

    public function debug($message, array $context = [])
    {
        if ($this->log != null) {
            $this->log->debug($message, $context);
        } else {
            $this->defaultLogger($message, $context);
        }
    }

    public function info($message, array $context = [])
    {
        if ($this->log != null) {
            $this->log->info($message, $context);
        } else {
            $this->defaultLogger($message, $context);
        }
    }
    public function warning($message, array $context = [])
    {
        if ($this->log != null) {
            $this->log->warning($message, $context);
        } else {
            $this->defaultLogger($message, $context);
        }
    }
}
