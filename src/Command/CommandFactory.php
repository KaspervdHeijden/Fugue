<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Collection\CollectionMap;
use Fugue\Container\ClassResolver;
use Fugue\Container\Container;

use function class_exists;

final class CommandFactory
{
    private ClassResolver $classResolver;
    private Container $container;

    public function __construct(
        ClassResolver $classResolver,
        Container $container
    ) {
        $this->classResolver = $classResolver;
        $this->container     = $container;
    }

    public function getForIdentifier(string $identifier): CommandInterface
    {
        if (! class_exists($identifier, true)) {
            throw InvalidCommandException::forUnknownIdentifier($identifier);
        }

        $command = $this->classResolver->resolve(
            $identifier,
            $this->container,
            new CollectionMap()
        );

        if (! $command instanceof CommandInterface) {
            throw InvalidCommandException::forInvalidClassType($identifier);
        }

        return $command;
    }
}
