<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

use FpDbTest\StringEscaper\StringEscaper;

class ParamResolverFactory
{
    private array $resolvers = [];

    public function __construct(
       private readonly StringEscaper $escaper,
    ) {
    }

    public function getResolver(?string $specifier): ParamResolver
    {
        $paramType = match ($specifier) {
            'd' => ParamType::INT,
            'f' => ParamType::FLOAT,
            'a' => ParamType::ARRAY,
            '#' => ParamType::IDENTIFIER,
            default => ParamType::DEFAULT,
        };

        if (isset($this->resolvers[$paramType->value])) {
            return $this->resolvers[$paramType->value];
        }

        $resolver = match ($paramType) {
            ParamType::INT => new IntParamResolver(),
            ParamType::FLOAT => new FloatParamResolver(),
            ParamType::ARRAY => new ArrayParamResolver($this->escaper),
            ParamType::IDENTIFIER => new IdentifierParamResolver(),
            ParamType::DEFAULT => new DefaultParamResolver($this->escaper),
        };

        $this->resolvers[$paramType->value] = $resolver;

        return $resolver;
    }
}
