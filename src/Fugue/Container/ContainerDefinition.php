<?php

declare(strict_types=1);

namespace Fugue\Container;

abstract class ContainerDefinition implements ContainerDefinitionInterface
{
    private mixed $definition;
    private string $name;

    public function __construct(string $name, mixed $definition)
    {
        if (! $this->isValidDefinition($definition)) {
            throw InvalidDefinitionTypeException::forDefinitionName($name);
        }

        $this->definition = $definition;
        $this->name       = $name;
    }

    protected function isValidDefinition(mixed $definition): bool
    {
        return true;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefinition(): mixed
    {
        return $this->definition;
    }

    abstract public function resolve(Container $container): mixed;
}
