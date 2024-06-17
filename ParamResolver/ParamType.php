<?php

namespace FpDbTest\ParamResolver;

enum ParamType: string
{
    case DEFAULT = 'default';
    case ARRAY = 'array';
    case INT = 'int';
    case FLOAT = 'float';
    case IDENTIFIER = 'identifier';
}
