<?php

declare(strict_types=1);

namespace Fugue\Collection;

use IteratorAggregate;
use ArrayIterator;
use ArrayAccess;
use Traversable;
use Countable;

use function array_key_exists;
use function array_key_first;
use function array_key_last;
use function array_reduce;
use function array_search;
use function array_merge;
use function array_slice;
use function array_keys;
use function is_string;
use function count;

abstract class Collection implements ArrayAccess, IteratorAggregate, Countable
{
    /** @var callable[]|string[] */
    private const TYPE_MAPPING = [
        'countable' => 'is_countable',
        'resource'  => 'is_resource',
        'callable'  => 'is_callable',
        'function'  => 'is_callable',
        'iterable'  => 'is_iterable',
        'numeric'   => 'is_numeric',
        'scalar'    => 'is_scalar',
        'object'    => 'is_object',
        'string'    => 'is_string',
        'array'     => 'is_array',
        'float'     => 'is_float',
        'double'    => 'is_float',
        'real'      => 'is_float',
        'boolean'   => 'is_bool',
        'bool'      => 'is_bool',
        'int'       => 'is_int',
        'integer'   => 'is_int',
    ];

    private array $elements = [];

    private ?string $type;

    public function __construct(
        iterable $elements = [],
        ?string $type      = null
    ) {
        $this->type = $type;
        foreach ($elements as $key => $value) {
            $this->set($value, $key);
        }
    }

    /**
     * Determines if this collection contains the supplied key.
     *
     * @param string|int|null $key The key to test for.
     * @return bool                TRUE if the key exists in this Map, FALSE otherwise.
     */
    public function containsKey($key): bool
    {
        return (bool)array_key_exists($key, $this->elements);
    }

    public function merge(self $other): self
    {
        return new static(
            array_merge($this->elements, $other->elements),
            $this->type
        );
    }

    public function offsetExists($offset): bool
    {
        return $this->containsKey($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset, null);
    }

    public function offsetSet($offset, $value): void
    {
        $this->set($value, $offset);
    }

    public function offsetUnset($offset): void
    {
        $this->unset($offset);
    }

    /**
     * Gets a value.
     *
     * @param string|int $key     The name of the value to get.
     * @param mixed      $default A default for if the value does not exist.
     *
     * @return mixed              The value found, or the default.
     */
    public function get($key, $default = null)
    {
        if (! $this->checkKey($key)) {
            throw InvalidTypeException::forKey(static::class);
        }

        return $this->elements[$key] ?? $default;
    }

    /**
     * Sets a value.
     *
     * @param mixed           $value The value to store.
     * @param string|int|null $key   The name of the value to set.
     */
    public function set($value, $key = null): void
    {
        if (! $this->checkKey($key)) {
            throw InvalidTypeException::forKey(static::class);
        }

        if (! $this->checkValue($value)) {
            throw InvalidTypeException::forValue(static::class);
        }

        if ($key === null) {
            $this->elements[] = $value;
        } else {
            $this->elements[$key] = $value;
        }
    }

    public function unset(...$keys): void
    {
        foreach ($keys as $key) {
            if (! $this->checkKey($key)) {
                throw InvalidTypeException::forKey(static::class);
            }

            unset($this->elements[$key]);
        }
    }

    /**
     * Filters the collection given a filter method.
     *
     * @param callable The filter method.
     * @return static  A new collection based on the
     *                 return values of the filter method.
     */
    public function filter(callable $filter): self
    {
        return new static(
            array_filter($this->elements, $filter),
            $this->type
        );
    }

    /**
     * Returns true if the calls to given method
     * returns the expected result for all elements.
     *
     * This implementation stops at the first failure.
     *
     * @param callable $conditionMethod The method performing the test on the elements.
     * @param mixed    $expectedResult  The expected result.
     *
     * @return bool                     TRUE if all method calls return TRUE, FALSE otherwise.
     */
    public function every(
        callable $conditionMethod,
        $expectedResult = true
    ): bool {
        foreach ($this->elements as $element) {
            if ($conditionMethod($element) !== $expectedResult) {
                return false;
            }
        }

        return true;
    }

    /**
     * Performs a reduce where this collection
     * elements are reduced to a single value.
     *
     * @param callable $combinator   Method returns a single value given two elements.
     * @param mixed    $initialValue The initial value to use.
     *
     * @return mixed   The single reduced value.
     */
    public function reduce(
        callable $combinator,
        $initialValue = null
    ) {
        return array_reduce(
            $this->elements,
            $combinator,
            $initialValue
        );
    }

    public function forEach(
        callable $filter,
        ?string $type = null
    ): self {
        return new static(
            array_map($filter, $this->elements),
            ($type !== null) ? $type : $this->type
        );
    }

    /**
     * Checks if the given value type is acceptable.
     *
     * @param mixed $value The value to check.
     * @return bool        TRUE if the value is to be accepted, FALSE otherwise.
     */
    protected function checkValue($value): bool
    {
        if (! is_string($this->type)) {
            return true;
        }

        if (isset(self::TYPE_MAPPING[$this->type])) {
            return (self::TYPE_MAPPING[$this->type])($value);
        }

        return ($value instanceof $this->type);
    }

    /**
     * Checks if the given key type is acceptable.
     *
     * @param string|int|null $key The key to check.
     * @return bool                TRUE if the key is acceptable, FALSE otherwise.
     */
    protected function checkKey($key): bool
    {
        return true;
    }

    /**
     * Clears the collection.
     */
    public function clear(): void
    {
        $this->elements = [];
    }

    /**
     * Gets the data in this collection as an array.
     *
     * @return array The elements of this collection as an array.
     */
    public function toArray(): array
    {
        return $this->elements;
    }

    /**
     * Determines if this collection contains the supplied value.
     *
     * @param mixed $element The element to test for.
     * @return bool          TRUE if the key exists in this Map, FALSE otherwise.
     */
    public function contains($element): bool
    {
        return array_search($element, $this->elements, true) !== false;
    }

    /**
     * Gets a list of all keys defined in this Collection.
     *
     * @return string[]|int[] List of keys.
     */
    public function keys(): array
    {
        return array_keys($this->elements);
    }

    /**
     * Gets a subset of this collection.
     *
     * @param int      $offset The start of the subset index.
     * @param int|null $length The length of the subset, of NULL for all.
     *
     * @return Collection      The subset collection.
     */
    public function slice(int $offset, ?int $length = null): self
    {
        return new static(
            array_slice(
                $this->elements,
                $offset,
                $length,
                true
            ),
            $this->type
        );
    }

    /**
     * Gets the first element.
     *
     * @return mixed The first element in this collection, or NULL if this collection is empty.
     */
    public function first()
    {
        $key = array_key_first($this->elements);
        if ($key === null) {
            return null;
        }

        return $this->elements[$key];
    }

    /**
     * Gets the last element.
     *
     * @return mixed The last element in this collection, or NULL if this collection is empty.
     */
    public function last()
    {
        $key = array_key_last($this->elements);
        if ($key === null) {
            return null;
        }

        return $this->elements[$key];
    }

    /**
     * Checks to see if this collection is empty.
     *
     * @return bool TRUE if this collection is empty, FALSE otherwise.
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
