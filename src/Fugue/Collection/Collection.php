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
use function array_values;
use function array_reduce;
use function array_search;
use function array_merge;
use function array_keys;
use function is_object;
use function is_string;
use function array_sum;
use function gettype;
use function count;
use function max;
use function min;

class Collection implements ArrayAccess, IteratorAggregate, Countable
{
    /** @var callable[]|string[] */
    private const TYPE_MAPPING = [
        'countable' => '\is_countable',
        'resource'  => '\is_resource',
        'callable'  => '\is_callable',
        'function'  => '\is_callable',
        'iterable'  => '\is_iterable',
        'numeric'   => '\is_numeric',
        'scalar'    => '\is_scalar',
        'object'    => '\is_object',
        'string'    => '\is_string',
        'array'     => '\is_array',
        'float'     => '\is_float',
        'double'    => '\is_float',
        'real'      => '\is_float',
        'boolean'   => '\is_bool',
        'bool'      => '\is_bool',
        'null'      => '\is_null',
        'int'       => '\is_int',
        'integer'   => '\is_int',
    ];

    /**
     * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
     * @var mixed[]
     */
    private array $elements = [];
    private ?string $type;

    final public function __construct(
        iterable $elements = [],
        ?string $type      = null
    ) {
        $this->type = $type;
        $this->push($elements);
    }

    public function containsKey($key): bool
    {
        return array_key_exists($key, $this->elements);
    }

    public function merge(array $other): static
    {
        return new static(
            array_merge($this->elements, $other),
            $this->type
        );
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->containsKey($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($value, $offset);
    }

    public function offsetUnset(mixed $offset): void
    {
        $this->unset($offset);
    }

    public function get(mixed $key, mixed $default = null): mixed
    {
        return $this->elements[$key] ?? $default;
    }

    public function set(mixed $value, mixed $key = null): void
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

    public function unset(mixed ...$keys): void
    {
        foreach ($keys as $key) {
            if (! $this->checkKey($key)) {
                throw InvalidTypeException::forKey(static::class);
            }

            unset($this->elements[$key]);
        }
    }

    public function filter(callable $filter): static
    {
        return new static(
            array_filter($this->elements, $filter),
            $this->type
        );
    }

    public function reduce(
        callable $combinator,
        mixed $initialValue = null
    ) {
        return array_reduce(
            $this->elements,
            $combinator,
            $initialValue
        );
    }

    public function implode(
        string $glue = '',
        ?callable $caster = null
    ): string {
        $caster = $caster ?: fn (mixed $element): string => (string)$element;
        return implode($glue, $this->map($caster));
    }

    public function map(callable $filter): array
    {
        return array_map($filter, $this->elements);
    }

    protected function checkValue(mixed $value): bool
    {
        if (! is_string($this->type)) {
            return true;
        }

        if (isset(self::TYPE_MAPPING[$this->type])) {
            return (self::TYPE_MAPPING[$this->type])($value);
        }

        return ($value instanceof $this->type);
    }

    protected function checkKey(mixed $key): bool
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

    public function values(): array
    {
        return array_values($this->elements);
    }

    public function contains(mixed $element): bool
    {
        return array_search($element, $this->elements, true) !== false;
    }

    public function keys(): array
    {
        return array_keys($this->elements);
    }

    public function first(): mixed
    {
        $key = array_key_first($this->elements);
        if ($key === null) {
            return null;
        }

        return $this->elements[$key];
    }

    public function last(): mixed
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
        mixed $expectedResult = true
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
        mixed $expectedResult = true
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

    public function sum(): int|float
    {
        return array_sum($this->values());
    }

    public function avg(): float
    {
        $count = $this->count();
        if ($count === 0) {
            return 0;
        }

        return (float)($this->sum() / $count);
    }

    public function max(): mixed
    {
        return max(...$this->values());
    }

    public function min(): mixed
    {
        return min(...$this->values());
    }

    public function push(iterable $elements): void
    {
        foreach ($elements as $key => $element) {
            $this->set($element, $key);
        }
    }

    public function cloneType(iterable $newElements = []): static
    {
        return new static($newElements, $this->type);
    }

    public static function forArray(iterable $elements): static
    {
        return new static($elements, 'array');
    }

    public static function forString(iterable $elements): static
    {
        return new static($elements, 'string');
    }

    public static function forInt(iterable $elements): static
    {
        return new static($elements, 'int');
    }

    public static function forFloat(iterable $elements): static
    {
        return new static($elements, 'float');
    }

    public static function forBool(iterable $elements): static
    {
        return new static($elements, 'bool');
    }

    public static function forAuto(iterable $elements): static
    {
        if ($elements instanceof (static::class)) {
            /** @var static $elements */
            return $elements->merge([]);
        }

        foreach ($elements as $element) {
            if ($element === null) {
                continue;
            }

            if (is_object($element)) {
                return new static($elements, $element::class);
            }

            $type = gettype($element);
            if (isset(self::TYPE_MAPPING[$type])) {
                return new static($elements, $type);
            }
        }

        return new static($elements);
    }

    public static function forMixed(iterable $elements): static
    {
        return new static($elements, null);
    }

    public static function forType(string $type, iterable $elements = []): static
    {
        return new static($elements, $type);
    }
}
