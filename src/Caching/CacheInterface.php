<?php

declare(strict_types=1);

namespace Fugue\Caching;

interface CacheInterface
{
    /**
     * Determines if a value is set for the given key.
     *
     * @param string $key The key to test for.
     * @return bool       TRUE if a value si set for the given key, FALSE otherwise.
     */
    public function hasValueForKey(string $key): bool;

    /**
     * Retrieves a value from cache.
     *
     * @param string $key             The key of the item to retrieve.
     *
     * @return mixed                  The value for the given key.
     * @throws ValueNotFoundException If no value is found for the given key.
     */
    public function retrieve(string $key);

    /**
     * Stores a value in cache.
     *
     * @param string $key  The key of the item to store.
     * @param mixed $value The value to store under the given key.
     */
    public function store(string $key, $value): void;
}
