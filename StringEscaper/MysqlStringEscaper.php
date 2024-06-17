<?php

namespace FpDbTest\StringEscaper;

use mysqli;

readonly class MysqlStringEscaper implements StringEscaper
{
    public function __construct(
        private mysqli $mysqli,
    ) {
    }

    public function escape(string $param): string
    {
        return $this->mysqli->real_escape_string($param);
    }
}
