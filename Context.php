<?php

declare(strict_types=1);

namespace FpDbTest;

use Exception;

final class Context
{
    private string $content = '';
    private bool $skip = false;

    public function __construct(
        private readonly ?Context $parent = null,
    ) {
    }

    public function createBlock(): Context
    {
        if ($this->parent !== null) {
            throw new Exception('Nested blocks are not allowed');
        }

        return new self($this);
    }

    public function closeBlock(): Context
    {
        if ($this->parent === null) {
            throw new Exception('Attempt to close non-existent block');
        }

        $this->parent->addContent($this->getContent());

        return $this->parent;
    }

    public function addContent(string $content): void
    {
        $this->content .= $content;
    }

    public function getContent(): string
    {
        return $this->skip ? '' : $this->content;
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
