<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

class IdentifierParamResolver implements ParamResolver
{
    public function resolve(mixed $arg): string
    {
        return implode(', ', array_map(static fn($arg): string => "`$arg`", (array)$arg));
    }
}
