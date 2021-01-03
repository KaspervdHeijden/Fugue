<?php

declare(strict_types=1);

namespace Fugue\Caching;

use function array_key_exists;

final class MemoryCache implements CacheInterface
{
    private array $items = [];

    public function hasEntry(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function retrieve(string $key)
    {
        if (! $this->hasEntry($key)) {
            throw ValueNotFoundException::forKey($key);
        }

        return $this->items[$key];
    }

    public function store(string $key, $value): void
    {
        $this->items[$key] = $value;
    }
}
