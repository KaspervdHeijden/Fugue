<?php

declare(strict_types=1);

namespace Fugue\Container;

abstract class ContainerDefinition implements ContainerDefinitionInterface
{
    private string $name;
    /** @var mixed */
    private $definition;

    public function __construct(string $name, $definition)
    {
        if (! $this->isValidDefinition($definition)) {
            throw InvalidDefinitionTypeException::forDefinitionName($name);
        }

        $this->definition = $definition;
        $this->name       = $name;
    }

    /** @noinspection PhpUnusedParameterInspection */
    protected function isValidDefinition($definition): bool
    {
        return true;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDefinition()
    {
        return $this->definition;
    }

    abstract public function resolve(Container $container);
}
