<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

use Exception;

class DefaultParamResolver implements ParamResolver
{
    public function resolve(mixed $arg): string
    {
        return match (true) {
            is_null($arg) => 'NULL',
            is_int($arg), is_float($arg) => "$arg",
            is_bool($arg) => (string)(int)$arg,
            is_string($arg) => "'$arg'",
            default => throw new Exception('Wrong argument type'),
        };
    }
}
