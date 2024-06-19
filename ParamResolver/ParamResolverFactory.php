<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

use Exception;
use FpDbTest\Scanner\TokenType;
use FpDbTest\StringEscaper\StringEscaper;

readonly class ParamResolverFactory
{
    public function __construct(
       private StringEscaper $escaper,
    ) {
    }

    public function getResolver(TokenType $tokenType): ParamResolver
    {
        return match ($tokenType) {
            TokenType::PARAM_INT => new IntParamResolver(),
            TokenType::PARAM_FLOAT => new FloatParamResolver(),
            TokenType::PARAM_ARRAY => new ArrayParamResolver($this->escaper),
            TokenType::PARAM_IDENTIFIER => new IdentifierParamResolver(),
            TokenType::PARAM_DEFAULT => new DefaultParamResolver($this->escaper),
            default => throw new Exception('Wrong param type'),
        };
    }
}
