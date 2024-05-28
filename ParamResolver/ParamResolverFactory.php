<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

class ParamResolverFactory
{
    public function getResolver(?string $specifier): ParamResolver
    {
        return match ($specifier) {
            'd' => new IntParamResolver(),
            'f' => new FloatParamResolver(),
            'a' => new ArrayParamResolver(),
            '#' => new IdentifierParamResolver(),
            default => new DefaultParamResolver(),
        };
    }
}
