<?php

declare(strict_types=1);

namespace FpDbTest\ParamResolver;

use Exception;
use mysqli;

readonly class ArrayParamResolver implements ParamResolver
{
    public function __construct(
        private mysqli $mysqli,
    ) {
    }

    public function resolve(mixed $arg): string
    {
        if (!is_array($arg)) {
            throw new Exception('Argument should be an array');
        }

        $keys = array_keys($arg);
        $isAssociative = array_keys($keys) !== $keys;

        if ($isAssociative) {
            $parts = [];
            foreach ($arg as $key => $value) {
                $parts[] = sprintf(
                    '%s = %s',
                    (new IdentifierParamResolver())->resolve($key),
                    (new DefaultParamResolver($this->mysqli))->resolve($value),
                );
            }

            return implode(', ', $parts);
        } else {
            return implode(
                ', ',
                array_map(
                    fn($item): string => (new DefaultParamResolver($this->mysqli))->resolve($item),
                    $arg,
                )
            );
        }
    }
}
