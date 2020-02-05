<?php

declare(strict_types=1);

namespace Fugue\Container;

use InvalidArgumentException;
use LogicException;
use ArrayAccess;
use Countable;

use function is_string;
use function count;

/**
 * Container class. This allows for lazy loading of objects within a defined scope.
 *
 * There are 3 ways to define a object:
 * 1. <var>TYPE_RAW</var>:       This just returns the $definition.
 * 2. <var>TYPE_SINGLETON</var>: This calls the $definition once, and returns it's return value, which is returned in any subsequent queries.
 * 3. <var>TYPE_FACTORY</var>:   The calls the $definition every time the field is accessed, and returns it's return value.
 *
 * While registering using ArrayAccess or dynamic properties is possible, it's type is always set to <var>TYPE_SINGLETON</var>.
 */
final class Container implements Countable, ArrayAccess
{
    /**
     * @var int Returns the value as given.
     */
    public const TYPE_RAW = 0;

    /**
     * @var int Calls the definer the first time only (lazy define). Subsequent requests return the result.
     */
    public const TYPE_SINGLETON = 1;

    /**
     * @var int Always return the return value from the definer.
     */
    public const TYPE_FACTORY = 2;

    /** @var ContainerDefinition[] */
    private $definitions = [];

    public function __construct(ContainerDefinition ...$definitions)
    {
        foreach ($definitions as $definition) {
            $this->register($definition);
        }
    }

    public function __set($name, $value)
    {
        $this->registerRaw($this->ensureStringName($name), $value);
    }

    public function __get($name)
    {
        return $this->resolve($this->ensureStringName($name));
    }

    public function __isset($name)
    {
        return $this->isRegistered((string)$name);
    }

    public function __unset($name)
    {
        $this->unregister((string)$name);
    }

    public function count(): int
    {
        return count($this->definitions);
    }

    public function offsetExists($offset)
    {
        return $this->isRegistered((string)$offset);
    }

    public function offsetGet($offset)
    {
        return $this->resolve($this->ensureStringName($offset));
    }

    public function offsetSet($offset, $value)
    {
        $this->registerRaw($this->ensureStringName($offset), $value);
    }

    public function offsetUnset($offset)
    {
        $this->unregister((string)$offset);
    }

    public function unregister(string $name): void
    {
        unset($this->definitions[(string)$name]);
    }

    public function isRegistered(string $name): bool
    {
        return isset($this->definitions[$name]);
    }

    /**
     * Gets an entity from the Container.
     *
     * @param string $name The name of the object to load.
     * @return mixed       The value/service/object, or null if the object isn't registered is no data.
     */
    public function resolve(string $name)
    {
        if (! $this->isRegistered($name)) {
            return null;
        }

        $definition = $this->definitions[$name];
        switch ($definition->getType()) {
            case self::TYPE_RAW:
                return $definition->getDefinition();
            case self::TYPE_FACTORY:
                return $definition->getDefinition()($this);
            case self::TYPE_SINGLETON:
                $this->registerRaw($name, $definition->getDefinition()($this));
                return $this->resolve($name);
            default:
                throw new LogicException(
                    "Invalid type ({$definition->getType()}) for '{$name}'."
                );
        }
    }

    private function registerRaw(string $name, $value): void
    {
        $this->register(ContainerDefinition::raw($name, $value));
    }

    /**
     * Registers a value/service/object.
     *
     * If you wish to bind a class instance that implements __call() by type RAW,
     * please specify self::TYPE_RAW implicitly, because those variables are methods.
     *
     * @param ContainerDefinition $definition The definition to register.
     */
    public function register(ContainerDefinition $definition): void
    {
        $this->definitions[$definition->getName()] = $definition;
    }

    private function ensureStringName($name): string
    {
        if (! is_string($name)) {
            throw new InvalidArgumentException('Names must be strings.');
        }

        return (string)$name;
    }
}
