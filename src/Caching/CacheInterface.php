<?php

declare(strict_types=1);

namespace Fugue\Caching;

interface CacheInterface
{
    public function hasValueForKey(string $key): bool;

    public function retrieve(string $key);

    public function store(string $key, $value): void;
}
