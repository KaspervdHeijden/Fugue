<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Core\Exception\ExceptionHandlerInterface;
use Fugue\Collection\CollectionList;
use Fugue\Logging\LoggerInterface;
use Throwable;

use function array_slice;
use function explode;

abstract class Command implements CommandInterface
{
    private ExceptionHandlerInterface $exceptionHandler;
    private LoggerInterface $logger;
    private string $name;

    public function __construct(
        LoggerInterface $logger,
        ExceptionHandlerInterface $exceptionHandler
    ) {
        $this->name             = (string)array_slice(explode('\\', static::class), -1)[0];
        $this->exceptionHandler = $exceptionHandler;
        $this->logger           = $logger;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getExceptionHandler(): ExceptionHandlerInterface
    {
        return $this->exceptionHandler;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function run(CollectionList $arguments): int
    {
        try {
            $this->logger->verbose("Starting {$this->name}");
            $this->execute($arguments);
            $this->logger->verbose("Completed {$this->name}");
        } catch (Throwable $throwable) {
            $this->getExceptionHandler()->handle($throwable);
            return (int)($throwable->getCode() ?: 1);
        }

        return 0;
    }

    /**
     * Method containing the logic to perform when running a command.
     */
    abstract protected function execute(CollectionList $arguments): void;
}
