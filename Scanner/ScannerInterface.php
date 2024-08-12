<?php

namespace FpDbTest\Scanner;

interface ScannerInterface
{
    /**
     * @return Token[]
     */
    public function getTokens(string $query): array;
}
