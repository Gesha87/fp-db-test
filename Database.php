<?php

namespace FpDbTest;

use Exception;
use FpDbTest\ParamResolver\DefaultParamResolver;
use FpDbTest\ParamResolver\ParamResolverFactory;
use FpDbTest\StringEscaper\MysqlStringEscaper;
use mysqli;

readonly class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private ParamResolverFactory $paramResolverFactory;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        $this->paramResolverFactory = new ParamResolverFactory(
            new MysqlStringEscaper($this->mysqli),
        );
    }

    public function buildQuery(string $query, array $args = []): string
    {
        $currentContext = new Context();
        for ($count = strlen($query), $i = 0; $i < $count; $i++) {
            $char = $query[$i];
            if ($char === '{') {
                $currentContext = $currentContext->createBlock();
            } elseif ($char === '}') {
                $currentContext = $currentContext->closeBlock();
            } elseif ($char === '?') {
                if (count($args) === 0) {
                    throw new Exception('Wrong count of arguments');
                }

                $arg = array_shift($args);
                if ($arg === $this->skip()) {
                    $currentContext->skip();
                }

                $paramResolver = $this->paramResolverFactory->getResolver($query[$i + 1] ?? null);
                $currentContext->addContent($paramResolver->resolve($arg));

                if (!$paramResolver instanceof DefaultParamResolver) {
                    $i++;
                }
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

                $currentContext->addContent($string);
            } else {
                $currentContext->addContent($char);
            }
        }

        $currentContext->check();

        return $currentContext->getContent();
    }

    public function skip(): string
    {
        return '___SKIP___';
    }
}
