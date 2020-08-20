<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use function strlen;

final class StringBuffer
{
    private string $buffer;

    public function __construct(string $value = '')
    {
        $this->buffer = $value;
    }

    public function clear(): void
    {
        $this->buffer = '';
    }

    public function value(): string
    {
        return $this->buffer;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function append(string $value): void
    {
        $this->buffer .= $value;
    }

    public function length(): int
    {
        return strlen($this->buffer);
    }
}
