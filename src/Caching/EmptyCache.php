<?php

declare(strict_types=1);

namespace Fugue\Caching;

final class EmptyCache implements CacheInterface
{
    public function retrieve(string $key)
    {
    }

    public function store(string $key, $value): void
    {
    }
}
