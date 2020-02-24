<?php

declare(strict_types=1);

namespace Fugue\Caching;

use Memcached;

final class MemcachedCache implements CacheInterface
{
    /** @var Memcached */
    private $memCached;

    public function __construct(Memcached $memCached)
    {
        $this->memCached = $memCached;
    }

    public function hasValueForKey(string $key): bool
    {
        $this->memCached->get($key);
        if ($this->memCached->getResultCode() === Memcached::RES_NOTFOUND) {
            return false;
        }

        return true;
    }

    public function retrieve(string $key)
    {
        $value = $this->memCached->get($key);
        if ($this->memCached->getResultCode() === Memcached::RES_NOTFOUND) {
            throw ValueNotFoundException::forKey($key);
        }

        return $value;
    }

    public function store(string $key, $value): void
    {
        if (! $this->memCached->replace($key, $value)) {
            $this->memCached->set($key, $value);
        }
    }
}
