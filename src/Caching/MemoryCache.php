<?php

declare(strict_types=1);

namespace Fugue\Caching;

use function array_key_exists;

final class MemoryCache implements CacheInterface
{
    /** @var mixed[] */
    private $items = [];

    public function hasValueForKey(string $key): bool
    {
        return array_key_exists($key, $this->items);
    }

    public function retrieve(string $key)
    {
        return $this->items[$key] ?? null;
    }

    public function store(string $key, $value): void
    {
        $this->items[$key] = $value;
    }
}
