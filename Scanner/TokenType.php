<?php

namespace FpDbTest\Scanner;

enum TokenType: string
{
    case CONTENT = 'content';
    case BLOCK_BEGIN = 'block_begin';
    case BLOCK_END = 'begin_end';
    case PARAM_DEFAULT = 'param_default';
    case PARAM_ARRAY = 'param_array';
    case PARAM_INT = 'param_int';
    case PARAM_FLOAT = 'param_float';
    case PARAM_IDENTIFIER = 'param_identifier';
}
