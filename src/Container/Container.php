<?php

declare(strict_types=1);

namespace Fugue\Container;

use InvalidArgumentException;
use ArrayAccess;
use Countable;

use function is_string;
use function count;

final class Container implements Countable, ArrayAccess
{
    /** @var ContainerDefinition[] */
    private array $definitions = [];

    public function __construct(ContainerDefinition ...$definitions)
    {
        foreach ($definitions as $definition) {
            $this->register($definition);
        }
    }

    public function __set($name, $value): void
    {
        $this->register(
            new RawContainerDefinition(
                $this->ensureStringName($name),
                $value
            )
        );
    }

    public function __get($name)
    {
        return $this->resolve($this->ensureStringName($name));
    }

    public function __isset($name): bool
    {
        return $this->isRegistered((string)$name);
    }

    public function __unset($name): void
    {
        $this->unregister((string)$name);
    }

    public function count(): int
    {
        return count($this->definitions);
    }

    public function offsetExists($offset): bool
    {
        return $this->isRegistered((string)$offset);
    }

    public function offsetGet($offset)
    {
        return $this->resolve($this->ensureStringName($offset));
    }

    public function offsetSet($offset, $value): void
    {
        $this->register(
            new RawContainerDefinition(
                $this->ensureStringName($offset),
                $value
            )
        );
    }

    public function offsetUnset($offset): void
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
     * @return mixed       The value/service/object, or null if the object isn't registered.
     */
    public function resolve(string $name)
    {
        if (! $this->isRegistered($name)) {
            return null;
        }

        return $this->definitions[$name]->resolve($this);
    }

    /**
     * Registers a value/service/object.
     *
     * If you wish to bind a class instance that implements __call() by type RAW,
     * please specify self::TYPE_RAW implicitly, because those variables are methods.
     */
    public function register(ContainerDefinition $definition): void
    {
        $this->definitions[$definition->getName()] = $definition;
    }

    private function ensureStringName($name): string
    {
        if (! is_string($name)) {
            throw new InvalidArgumentException('Name must be a string');
        }

        return (string)$name;
    }
}
