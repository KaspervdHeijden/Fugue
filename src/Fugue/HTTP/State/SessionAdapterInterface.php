<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

interface SessionAdapterInterface
{
    public function start(): void;

    public function get(string $name);

    public function set(string $name, $value): void;

    public function has(string $name): bool;

    public function unset(string $name): void;

    public function clear(): void;

    public function close(): void;
}
