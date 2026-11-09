<?php

declare(strict_types=1);

namespace ParserCompatibility;

function rewritten(string $value): string
{
    return 'aliased:' . strtoupper($value);
}

class InterceptionFixture
{
    public int $state = 0;

    public function value(int $input): int
    {
        return $input + 1;
    }

    public static function staticValue(int $input): int
    {
        return $input * 2;
    }

    public function setValue(int $input): void
    {
        $this->state = $input;
    }

    public function invokeHidden(int $input): int
    {
        return $this->protectedValue($input) + $this->privateValue($input);
    }

    protected function protectedValue(int $input): int
    {
        return $input * 2;
    }

    private function privateValue(int $input): int
    {
        return $input + 10;
    }

    public function fail(\Exception $error): void
    {
        throw $error;
    }

    public function alias(string $input): string
    {
        return strrev($input);
    }
}
