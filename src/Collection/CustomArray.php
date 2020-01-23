<?php

declare(strict_types=1);

namespace Fugue\Collection;

use UnexpectedValueException;
use IteratorAggregate;
use ArrayIterator;
use ArrayAccess;
use Traversable;
use Countable;

use function array_key_exists;
use function array_search;
use function array_merge;
use function array_keys;
use function count;

abstract class CustomArray implements ArrayAccess, IteratorAggregate, Countable
{
    /** @var mixed[] */
    private $elements = [];

    public function __construct(iterable $elements = [])
    {
        foreach ($elements as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Determines if this Map contains the supplied key.
     *
     * @param string|int $key The key to test for.
     * @return bool           TRUE if the key exists in this Map, FALSE otherwise.
     */
    public function containsKey($key): bool
    {
        return (bool)array_key_exists($key, $this->elements);
    }

    public function merge(self $other): self
    {
        return new static(
            array_merge($this->elements, $other->elements)
        );
    }

    public function offsetExists($offset)
    {
        return $this->containsKey($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset, null);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->unset($offset);
    }

    /**
     * Gets a value.
     *
     * @param string|int $key     The name of the value to get.
     * @param mixed      $default A default, if the value does not exist.
     *
     * @return mixed              The value, or the default.
     */
    public function get($key, $default = null)
    {
        return $this->elements[$key] ?? $default;
    }

    /**
     * Sets a value.
     *
     * @param string|int $key   The name of the value to set.
     * @param mixed      $value The value to store.
     */
    public function set($key, $value): void
    {
        if (! $this->checkKey($key)) {
            throw new UnexpectedValueException(
                'Invalid key for ' . static::class
            );
        }

        if (! $this->checkValue($value)) {
            throw new UnexpectedValueException(
                'Invalid value for ' . static::class
            );
        }

        if ($key === null) {
            $this->elements[] = $value;
        } else {
            $this->elements[$key] = $value;
        }
    }

    /**
     * Deletes a value.
     *
     * @param string|int $key The variable to delete.
     */
    public function unset($key): void
    {
        unset($this->elements[$key]);
    }

    public function filter(callable $filter): self
    {
        return new static(
            array_filter($this->elements, $filter)
        );
    }

    public function forEach(callable $filter): self
    {
        return new static(
            array_map($filter, $this->elements)
        );
    }

    /**
     * @param mixed $value The value to check.
     * @return bool        TRUE if the value is OK, FALSE otherwise.
     */
    protected function checkValue($value): bool
    {
        return true;
    }

    /**
     * @param string|int|null $key The key to check.
     * @return bool        TRUE if the value is OK, FALSE otherwise.
     */
    protected function checkKey($key): bool
    {
        return true;
    }

    /**
     * Clears the Collection.
     */
    public function clear(): void
    {
        $this->elements = [];
    }

    /**
     * Gets the data in this Map as an array.
     *
     * @return array The elements of this CustomArray as an array.
     */
    public function all(): array
    {
        return $this->elements;
    }

    /**
     * Determines if this Map contains the supplied value.
     *
     * @param mixed $element The element to test for.
     * @return bool          TRUE if the key exists in this Map, FALSE otherwise.
     */
    public function contains($element): bool
    {
        return array_search($element, $this->elements, true) !== false;
    }

    /**
     * Gets a list of all keys defined in this Map.
     *
     * @return string[]|int[] List of keys.
     */
    public function keys(): array
    {
        return array_keys($this->elements);
    }

    /**
     * Checks to see if this Map is empty.
     *
     * @return bool TRUE if this Map is empty, FALSE otherwise.
     */
    public function isEmpty(): bool
    {
        return count($this->elements) === 0;
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->elements);
    }

    public function count(): int
    {
        return count($this->elements);
    }
}
