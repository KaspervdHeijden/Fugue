<?php

declare(strict_types=1);

namespace Fugue\Caching;

final class EmptyCache implements CacheInterface
{
    public function hasValueForKey(string $key): bool
    {
        return false;
    }

    public function retrieve(string $key)
    {
        throw ValueNotFoundException::forKey($key);
    }

    public function store(string $key, $value): void
    {
    }
}
