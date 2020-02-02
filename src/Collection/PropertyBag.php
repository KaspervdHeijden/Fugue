<?php

declare(strict_types=1);

namespace Fugue\Collection;

final class PropertyBag extends CollectionMap
{
    /**
     * Gets an integer value from this PropertyBag.
     *
     * @param string|int $key     The name of the value to get.
     * @param int        $default A default, if the value does not exist.
     *
     * @return int                The value, or the default.
     */
    public function getInt($key, int $default = 0): int
    {
        return (int)$this->get($key, $default);
    }

    /**
     * Gets a boolean value from this PropertyBag.
     *
     * @param string|int $key     The name of the value to get.
     * @param bool       $default A default, if the value does not exist.
     *
     * @return bool               The value, or the default.
     */
    public function getBool($key, bool $default = false): bool
    {
        return (bool)$this->get($key, $default);
    }

    /**
     * Gets an float value from this PropertyBag.
     *
     * @param string|int $key      The name of the value to get.
     * @param float      $default  A default, if the value does not exist.
     *
     * @return float               The value, or the default.
     */
    public function getFloat($key, float $default = 0.00): float
    {
        return (float)$this->get($key, $default);
    }

    /**
     * Gets an string value from this PropertyBag.
     *
     * @param string|int $key     The name of the value to get.
     * @param string     $default A default, if the value does not exist.
     *
     * @return string             The value, or the default.
     */
    public function getString($key, string $default = ''): string
    {
        return (string)$this->get($key, $default);
    }

    /**
     * Gets an array value from this PropertyBag.
     *
     * @param string|int $key     The name of the value to get.
     * @param array      $default A default, if the value does not exist.
     *
     * @return array              The value, or the default.
     */
    public function getArray($key, array $default = []): array
    {
        return (array)$this->get($key, $default);
    }
}
