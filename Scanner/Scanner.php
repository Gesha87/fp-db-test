<?php

namespace FpDbTest\Scanner;

use Exception;
use Generator;

readonly class Scanner
{
    public function __construct(
        private string $query,
    ) {
    }

    /**
     * @return Generator<TokenType, string>
     */
    public function getTokens(): Generator
    {
        for ($count = strlen($this->query), $i = 0; $i < $count; $i++) {
            $char = $this->query[$i];
            if ($char === '{') {
                yield TokenType::BLOCK_BEGIN => $char;
            } elseif ($char === '}') {
                yield TokenType::BLOCK_END => $char;
            } elseif ($char === '?') {
                $specifier = $this->query[$i + 1] ?? null;
                $tokenType = match ($specifier) {
                    'd' => TokenType::PARAM_INT,
                    'f' => TokenType::PARAM_FLOAT,
                    'a' => TokenType::PARAM_ARRAY,
                    '#' => TokenType::PARAM_IDENTIFIER,
                    default => TokenType::PARAM_DEFAULT,
                };

                $param = $char;
                if ($tokenType !== TokenType::PARAM_DEFAULT) {
                    $param .= $specifier;
                    $i++;
                }

                yield $tokenType => $param;
            } elseif ($char === "'") {
                $string = $char;

                do {
                    $char = $this->query[++$i] ?? null;
                    if ($char === null) {
                        throw new Exception('Wrong template');
                    }

                    $string .= $char;

                    if ($char === "'") {
                        $nextChar = $this->query[$i + 1] ?? null;
                        if ($nextChar === "'") {
                            $string .= $nextChar;
                            $char = null;
                            $i++;
                        }
                    }
                    if ($char === '\\') {
                        $nextChar = $this->query[$i + 1] ?? null;
                        if ($nextChar !== null) {
                            $string .= $nextChar;
                            $char = null;
                            $i++;
                        }
                    }
                } while ($char !== "'");

                yield TokenType::CONTENT => $string;
            } else {
                $string = $char;
                $char = $this->query[$i + 1] ?? null;
                while ($char !== null && ! in_array($char, ['{', '}', '?', "'"])) {
                    $string .= $char;
                    $i++;
                    $char = $this->query[$i + 1] ?? null;
                }

                yield TokenType::CONTENT => $string;
            }
        }
    }
}
