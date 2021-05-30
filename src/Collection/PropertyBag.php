<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_string;

final class PropertyBag extends CollectionMap
{
    protected function checkKey(mixed $key): bool
    {
        return parent::checkKey($key) && is_string($key);
    }

    public function getInt(string $key, int $default = 0): int
    {
        return (int)$this->get($key, $default);
    }

    public function getBool(string $key, bool $default = false): bool
    {
        return (bool)$this->get($key, $default);
    }

    public function getFloat(string $key, float $default = 0.00): float
    {
        return (float)$this->get($key, $default);
    }

    public function getString(string $key, string $default = ''): string
    {
        return (string)$this->get($key, $default);
    }

    public function getArray(string $key, array $default = []): array
    {
        return (array)$this->get($key, $default);
    }
}
