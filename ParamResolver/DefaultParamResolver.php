<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

use Exception;
use FpDbTest\StringEscaper\StringEscaper;

readonly class DefaultParamResolver implements ParamResolver
{
    public function __construct(
        private StringEscaper $escaper,
    ) {
    }

    public function resolve(mixed $arg): string
    {
        return match (true) {
            is_null($arg) => 'NULL',
            is_int($arg), is_float($arg) => "$arg",
            is_bool($arg) => (string)(int)$arg,
            is_string($arg) => sprintf("'%s'", $this->escaper->escape($arg)),
            default => throw new Exception('Wrong argument type'),
        };
    }
}
