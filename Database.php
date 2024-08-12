<?php

namespace FpDbTest;

use Exception;
use FpDbTest\Context\Context;
use FpDbTest\Context\ContextType;
use FpDbTest\ParamResolver\ParamResolverFactory;
use FpDbTest\Scanner\Scanner;
use FpDbTest\Scanner\ScannerInterface;
use FpDbTest\Scanner\ScannerWithCache;
use FpDbTest\Scanner\TokenType;
use FpDbTest\StringEscaper\MysqlStringEscaper;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private ParamResolverFactory $paramResolverFactory;
    private ScannerInterface $scanner;
    private SkipValue $skipValue;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        $this->paramResolverFactory = new ParamResolverFactory(
            new MysqlStringEscaper($this->mysqli),
        );
        $this->scanner = new ScannerWithCache(new Scanner());
        $this->skipValue = new SkipValue();
    }

    public function buildQuery(string $query, array $args = []): string
    {
        $currentContext = new Context();
        $tokens = $this->scanner->getTokens($query);

        foreach ($tokens as $token) {
            switch ($token->type) {
                case TokenType::CONTENT:
                    $currentContext->addContent($token->content);
                    break;
                case TokenType::BLOCK_BEGIN:
                    $currentContext = $currentContext->addContext(ContextType::BLOCK);
                    break;
                case TokenType::BLOCK_END:
                    $currentContext = $currentContext->closeContext();
                    break;
                case TokenType::PARAM_DEFAULT:
                case TokenType::PARAM_ARRAY:
                case TokenType::PARAM_INT:
                case TokenType::PARAM_FLOAT:
                case TokenType::PARAM_IDENTIFIER:
                    if (count($args) === 0) {
                        throw new Exception('Wrong count of arguments');
                    }

                    $arg = array_shift($args);
                    if ($arg === $this->skip()) {
                        $currentContext->skip();
                        continue 2;
                    }

                    if (! $currentContext->isSkipped()) {
                        $paramResolver = $this->paramResolverFactory->getResolver($token->type);
                        $currentContext->addContent($paramResolver->resolve($arg));
                    }
                    break;
                default:
                    throw new Exception('Wrong token type');
            }
        }

        $currentContext->check();

        return $currentContext->getContent();
    }

    public function skip(): SkipValue
    {
        return $this->skipValue;
    }
}
