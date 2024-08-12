<?php

namespace FpDbTest\Scanner;

class ScannerWithCache implements ScannerInterface
{
    /**
     * @var array<string, Token[]>
     */
    private array $tokens = [];

    public function __construct(
        private readonly ScannerInterface $scanner,
    ) {
    }

    public function getTokens(string $query): array
    {
        if (! isset($this->tokens[$query])) {
            $this->tokens[$query] = $this->scanner->getTokens($query);
        }

        return $this->tokens[$query];
    }
}