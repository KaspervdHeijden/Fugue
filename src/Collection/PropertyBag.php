<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_string;

final class PropertyBag extends CollectionMap
{
    protected function checkKey($key): bool
    {
        return is_string($key);
    }

    /**
     * Gets an integer value from this PropertyBag.
     *
     * @param string $key     The name of the value to get.
     * @param int    $default A default, if the value does not exist.
     *
     * @return int   The value, or the default.
     */
    public function getInt(string $key, int $default = 0): int
    {
        return (int)$this->get($key, $default);
    }

    /**
     * Gets a boolean value from this PropertyBag.
     *
     * @param string $key     The name of the value to get.
     * @param bool   $default A default, if the value does not exist.
     *
     * @return bool  The value, or the default.
     */
    public function getBool(string $key, bool $default = false): bool
    {
        return (bool)$this->get($key, $default);
    }

    /**
     * Gets an float value from this PropertyBag.
     *
     * @param string $key      The name of the value to get.
     * @param float  $default  A default, if the value does not exist.
     *
     * @return float The value, or the default.
     */
    public function getFloat(string $key, float $default = 0.00): float
    {
        return (float)$this->get($key, $default);
    }

    /**
     * Gets an string value from this PropertyBag.
     *
     * @param string  $key     The name of the value to get.
     * @param string  $default A default, if the value does not exist.
     *
     * @return string The value, or the default.
     */
    public function getString(string $key, string $default = ''): string
    {
        return (string)$this->get($key, $default);
    }

    /**
     * Gets an array value from this PropertyBag.
     *
     * @param string $key     The name of the value to get.
     * @param array  $default A default, if the value does not exist.
     *
     * @return array The value, or the default.
     */
    public function getArray(string $key, array $default = []): array
    {
        return (array)$this->get($key, $default);
    }
}
