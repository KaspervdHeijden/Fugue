<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Collection\CollectionMap;
use Fugue\Core\Exception\ExceptionHandlerInterface;
use Fugue\Container\ClassResolver;
use Fugue\Logging\LoggerInterface;
use Fugue\Container\Container;

use function class_exists;

final class CommandFactory
{
    /** @var ExceptionHandlerInterface */
    private $exceptionHandler;

    /** @var ClassResolver */
    private $classResolver;

    /** @var Container */
    private $container;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ExceptionHandlerInterface $exceptionHandler,
        ClassResolver $classResolver,
        LoggerInterface $logger,
        Container $container
    ) {
        $this->exceptionHandler = $exceptionHandler;
        $this->classResolver    = $classResolver;
        $this->container        = $container;
        $this->logger           = $logger;
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
