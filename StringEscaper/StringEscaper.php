<?php

namespace FpDbTest\StringEscaper;

interface StringEscaper
{
    public function escape(string $param): string;
}
