<?php

namespace FpDbTest;

use Exception;
use FpDbTest\ParamResolver\DefaultParamResolver;
use FpDbTest\ParamResolver\ParamResolverFactory;
use mysqli;

readonly class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private ParamResolverFactory $paramResolverFactory;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        $this->paramResolverFactory = new ParamResolverFactory($this->mysqli);
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
            } else {
                $currentContext->addContent($char);
            }
        }

        $currentContext->check();

        return $currentContext->getContent();
    }

    public function skip(): int
    {
        return 666;
    }
}
