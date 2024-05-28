<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

readonly class IntParamResolver implements ParamResolver
{
    public function resolve(mixed $arg): string
    {
        return $arg === null ? 'NULL' : (string)(int)$arg;
    }
}
