<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

use Exception;
use FpDbTest\StringEscaper\StringEscaper;

readonly class ArrayParamResolver implements ParamResolver
{
    public function __construct(
        private StringEscaper $escaper,
    ) {
    }

    public function resolve(mixed $arg): string
    {
        if (!is_array($arg)) {
            throw new Exception('Argument should be an array');
        }
        if ($arg === []) {
            throw new Exception('Empty array not allowed');
        }

        $keys = array_keys($arg);
        $isAssociative = array_keys($keys) !== $keys;

        $defaultResolver = new DefaultParamResolver($this->escaper);
        if ($isAssociative) {
            $parts = [];
            $identifierResolver = new IdentifierParamResolver();
            foreach ($arg as $key => $value) {
                $parts[] = sprintf(
                    '%s = %s',
                    $identifierResolver->resolve($key),
                    $defaultResolver->resolve($value),
                );
            }

            return implode(', ', $parts);
        } else {
            return implode(
                ', ',
                array_map(
                    fn($item): string => $defaultResolver->resolve($item),
                    $arg,
                )
            );
        }
    }
}
