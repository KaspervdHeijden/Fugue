<?php

declare(strict_types=1);

namespace Fugue\Caching;

use Redis;

final class RedisCache implements CacheInterface
{
    /** @var Redis */
    private $redis;

    public function __construct(Redis $redis)
    {
        $this->redis = $redis;
    }

    public function hasValueForKey(string $key): bool
    {
        return (bool)$this->redis->exists($key);
    }

    public function retrieve(string $key)
    {
        if (! $this->hasValueForKey($key)) {
            throw ValueNotFoundException::forKey($key);
        }

        return $this->redis->get($key);
    }

    public function store(string $key, $value): void
    {
        $this->redis->set($key, $value);
    }
}
