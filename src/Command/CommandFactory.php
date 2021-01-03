<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Core\ClassLoader\ClassLoaderInterface;
use Fugue\Collection\CollectionMap;
use Fugue\Container\ClassResolver;
use Fugue\Container\Container;

final class CommandFactory
{
    private ClassLoaderInterface $classLoader;
    private ClassResolver $classResolver;
    private Container $container;

    public function __construct(
        ClassLoaderInterface $classLoader,
        ClassResolver $classResolver,
        Container $container
    ) {
        $this->classResolver = $classResolver;
        $this->classLoader   = $classLoader;
        $this->container     = $container;
    }

    public function getForIdentifier(string $identifier): CommandInterface
    {
        if (! $this->classLoader->exists($identifier, true)) {
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
