<?php

declare(strict_types=1);

namespace FpDbTest\Context;

use Exception;

final class Context
{
    private array $content = [];
    private bool $skip = false;

    public function __construct(
        private readonly ?Context $parent = null,
        private readonly ContextType $type = ContextType::QUERY,
    ) {
    }

    public function addContext(ContextType $type): Context
    {
        if ($this->type === ContextType::BLOCK) {
            throw new Exception('Nested blocks are not allowed');
        }

        return new self($this, $type);
    }

    public function closeContext(): Context
    {
        if ($this->type !== ContextType::BLOCK) {
            throw new Exception('Attempt to close non-existent block');
        }
        $this->parent->addContent($this->getContent());

        return $this->parent;
    }

    public function addContent(string $content): void
    {
        $this->content[] = $content;
    }

    public function getContent(): string
    {
        return $this->skip ? '' : implode('', $this->content);
    }

    public function skip(): void
    {
        if ($this->parent !== null) {
            $this->skip = true;
        }
    }

    public function check(): void
    {
        if ($this->parent !== null) {
            throw new Exception('Wrong template');
        }
    }

    public function isSkipped(): bool
    {
        return $this->skip;
    }
}
