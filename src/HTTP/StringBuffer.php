<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use Countable;

use function mb_strlen;
use function strlen;

final class StringBuffer implements Countable
{
    private string $value;

    public function __construct(string $value = '')
    {
        $this->value = $value;
    }

    public function clear(): void
    {
        $this->value = '';
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function append(string $value): void
    {
        $this->value .= $value;
    }

    public function size(): int
    {
        return strlen($this->value);
    }

    public function length(): int
    {
        return mb_strlen($this->value);
    }

    public function count(): int
    {
        return $this->length();
    }
}
