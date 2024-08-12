<?php

namespace FpDbTest\Scanner;

use Exception;

class Scanner implements ScannerInterface
{
    /**
     * @return Token[]
     */
    public function getTokens(string $query): array
    {
        $result = [];
        for ($count = strlen($query), $i = 0; $i < $count; $i++) {
            $char = $query[$i];
            if ($char === '{') {
                $result[] = new Token(TokenType::BLOCK_BEGIN, $char);
            } elseif ($char === '}') {
                $result[] = new Token(TokenType::BLOCK_END, $char);
            } elseif ($char === '?') {
                $specifier = $query[$i + 1] ?? null;
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

                $result[] = new Token($tokenType, $param);
            } elseif ($char === "'") {
                $string = $char;

                do {
                    $char = $query[++$i] ?? null;
                    if ($char === null) {
                        throw new Exception('Wrong template');
                    }

                    $string .= $char;

                    if ($char === "'") {
                        $nextChar = $query[$i + 1] ?? null;
                        if ($nextChar === "'") {
                            $string .= $nextChar;
                            $char = null;
                            $i++;
                        }
                    }
                    if ($char === '\\') {
                        $nextChar = $query[$i + 1] ?? null;
                        if ($nextChar !== null) {
                            $string .= $nextChar;
                            $char = null;
                            $i++;
                        }
                    }
                } while ($char !== "'");

                $result[] = new Token(TokenType::CONTENT, $string);
            } else {
                $string = $char;
                $char = $query[$i + 1] ?? null;
                while ($char !== null && ! in_array($char, ['{', '}', '?', "'"])) {
                    $string .= $char;
                    $i++;
                    $char = $query[$i + 1] ?? null;
                }

                $result[] = new Token(TokenType::CONTENT, $string);
            }
        }

        return $result;
    }
}
