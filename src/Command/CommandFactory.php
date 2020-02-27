<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Collection\CollectionMap;
use Fugue\Container\ClassResolver;
use Fugue\Container\Container;

use function class_exists;

final class CommandFactory
{
    /** @var ClassResolver */
    private $classResolver;

    /** @var Container */
    private $container;

    public function __construct(
        ClassResolver $classResolver,
        Container $container
    ) {
        $this->classResolver = $classResolver;
        $this->container     = $container;
    }

    public function getForIdentifier(string $identifier): CommandInterface
    {
        if (class_exists($identifier)) {
            return $this->classResolver->resolve(
                $identifier,
                $this->container,
                new CollectionMap()
            );
        }

        throw InvalidCommandException::forUnknownIdentifier($identifier);
    }
}
