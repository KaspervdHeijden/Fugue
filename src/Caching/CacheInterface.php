<?php

declare(strict_types=1);

namespace Fugue\Caching;

interface CacheInterface
{
    public function hasEntry(string $key): bool;

    public function retrieve(string $key);

    public function store(string $key, $value): void;
}
