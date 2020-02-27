<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

final class EmptySessionAdapter implements SessionAdapterInterface
{
    public function clear(): void
    {
    }

    public function get(string $name)
    {
        return null;
    }

    public function has(string $name): bool
    {
        return false;
    }

    public function set(string $name, $value): void
    {
    }

    public function start(SessionSettings $settings): void
    {
    }

    public function unset(string $name): void
    {
    }

    public function close(): void
    {
    }
}
