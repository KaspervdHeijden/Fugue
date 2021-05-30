<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Container\Container;
use Fugue\Core\Kernel;

final class CommandFactory
{
    private Container $container;
    private Kernel $kernel;

    public function __construct(Kernel $kernel, Container $container)
    {
        $this->container = $container;
        $this->kernel    = $kernel;
    }

    public function getForIdentifier(string $identifier): CommandInterface
    {
        if (! $this->kernel->getClassLoader()->exists($identifier, true)) {
            throw InvalidCommandException::forUnknownIdentifier($identifier);
        }

        $command = $this->kernel->resolveClass($identifier, $this->container);
        if (! $command instanceof CommandInterface) {
            throw InvalidCommandException::forInvalidClassType($identifier);
        }

        return $command;
    }
}
