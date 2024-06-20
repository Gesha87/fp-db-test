<?php

declare(strict_types=1);

namespace FpDbTest\Scanner;

readonly class Token
{
    public function __construct(
        public TokenType $type,
        public string $content,
    ) {
    }
}
