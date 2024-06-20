<?php

namespace FpDbTest;

use Exception;
use FpDbTest\ParamResolver\ParamResolverFactory;
use FpDbTest\Scanner\Scanner;
use FpDbTest\Scanner\TokenType;
use FpDbTest\StringEscaper\MysqlStringEscaper;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private ParamResolverFactory $paramResolverFactory;
    private array $tokens;

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
        $scanner = new Scanner($query);

        if (! isset($this->tokens[$query])) {
            $this->tokens[$query] = $scanner->getTokens();
        }

        foreach ($this->tokens[$query] as $token) {
            switch ($token->type) {
                case TokenType::CONTENT:
                    $currentContext->addContent($token->content);
                    break;
                case TokenType::BLOCK_BEGIN:
                    $currentContext = $currentContext->createBlock();
                    break;
                case TokenType::BLOCK_END:
                    $currentContext = $currentContext->closeBlock();
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

    public function skip(): string
    {
        return '___SKIP___';
    }
}
