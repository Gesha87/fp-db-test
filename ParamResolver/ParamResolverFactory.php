<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

use mysqli;

readonly class ParamResolverFactory
{
    public function __construct(
       private mysqli $mysqli,
    ) {
    }

    public function getResolver(?string $specifier): ParamResolver
    {
        return match ($specifier) {
            'd' => new IntParamResolver(),
            'f' => new FloatParamResolver(),
            'a' => new ArrayParamResolver($this->mysqli),
            '#' => new IdentifierParamResolver(),
            default => new DefaultParamResolver($this->mysqli),
        };
    }
}
