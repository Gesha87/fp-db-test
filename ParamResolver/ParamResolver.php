<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

interface ParamResolver
{
    public function resolve(mixed $arg): string;
}
