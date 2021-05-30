<?php

declare(strict_types=1);

namespace Fugue\Caching;

interface CacheInterface
{
    public function hasEntry(string $key): bool;

    public function retrieve(string $key): mixed;

    public function store(string $key, mixed $value): void;
}
