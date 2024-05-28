<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

class FloatParamResolver implements ParamResolver
{
    public function resolve(mixed $arg): string
    {
        return $arg === null ? 'NULL' : (string)(float)$arg;
    }
}
