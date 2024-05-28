<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

use Exception;
use mysqli;

readonly class DefaultParamResolver implements ParamResolver
{
    public function __construct(
        private mysqli $mysqli,
    ) {
    }

    public function resolve(mixed $arg): string
    {
        return match (true) {
            is_null($arg) => 'NULL',
            is_int($arg), is_float($arg) => "$arg",
            is_bool($arg) => (string)(int)$arg,
            is_string($arg) => sprintf("'%s'", $this->mysqli->real_escape_string($arg)),
            default => throw new Exception('Wrong argument type'),
        };
    }
}
