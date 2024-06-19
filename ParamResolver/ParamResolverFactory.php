<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

use Exception;
use FpDbTest\Scanner\TokenType;
use FpDbTest\StringEscaper\StringEscaper;

class ParamResolverFactory
{
    private array $resolvers = [];

    public function __construct(
       private readonly StringEscaper $escaper,
    ) {
    }

    public function getResolver(TokenType $tokenType): ParamResolver
    {
        if (! isset($this->resolvers[$tokenType->value])) {
            $this->resolvers[$tokenType->value] = match ($tokenType) {
                TokenType::PARAM_INT => new IntParamResolver(),
                TokenType::PARAM_FLOAT => new FloatParamResolver(),
                TokenType::PARAM_ARRAY => new ArrayParamResolver($this->escaper),
                TokenType::PARAM_IDENTIFIER => new IdentifierParamResolver(),
                TokenType::PARAM_DEFAULT => new DefaultParamResolver($this->escaper),
                default => throw new Exception('Wrong param type'),
            };
        }

        return $this->resolvers[$tokenType->value];
    }
}
