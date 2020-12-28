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

    final public function __construct(
        iterable $elements = [],
        ?string $type      = null
    ) {
        $this->type = $type;
        foreach ($elements as $key => $value) {
            $this->set($value, $key);
        }
    }

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

    public function get($key, $default = null)
    {
        if (! $this->checkKey($key)) {
            throw InvalidTypeException::forKey(static::class);
        }

        return $this->elements[$key] ?? $default;
    }

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

    public function filter(callable $filter): self
    {
        return new static(
            array_filter($this->elements, $filter),
            $this->type
        );
    }

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

    protected function checkKey($key): bool
    {
        return true;
    }

    public function clear(): void
    {
        $this->elements = [];
    }

    public function toArray(): array
    {
        return $this->elements;
    }

    public function contains($element): bool
    {
        return array_search($element, $this->elements, true) !== false;
    }

    public function keys(): array
    {
        return array_keys($this->elements);
    }

    public function slice(
        int $offset,
        ?int $length  = null,
        ?string $type = null
    ): self
    {
        return new static(
            array_slice(
                $this->elements,
                $offset,
                $length,
                true
            ),
            ($type !== null) ? $type : $this->type
        );
    }

    public function first()
    {
        $key = array_key_first($this->elements);
        if ($key === null) {
            return null;
        }

        return $this->elements[$key];
    }

    public function last()
    {
        $key = array_key_last($this->elements);
        if ($key === null) {
            return null;
        }

        return $this->elements[$key];
    }

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

    public function every(
        callable $checker,
        $expectedResult = true
    ): bool {
        foreach ($this->elements as $element) {
            if ($checker($element) !== $expectedResult) {
                return false;
            }
        }

        return true;
    }

    public function any(
        callable $checker,
        $expectedResult = true
    ): bool {
        if ($this->isEmpty()) {
            return true;
        }

        foreach ($this->elements as $key => $element) {
            if ($checker($element, $key) === $expectedResult) {
                return true;
            }
        }

        return false;
    }
}
