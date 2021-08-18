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
    public const DEFAULT_EXCEPTION_EXITCODE = 1;

    private ExceptionHandlerInterface $exceptionHandler;
    private LoggerInterface $logger;
    private string $name;

    public function __construct(
        LoggerInterface $logger,
        ExceptionHandlerInterface $exceptionHandler,
        ?string $name = null
    ) {
        $this->name             = $name ?: (string)array_slice(explode('\\', static::class), -1)[0];
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
            $this->logger->verbose("Starting {$this->getName()}");
            $exitCode = $this->execute($arguments);
            $this->logger->verbose("Completed {$this->getName()}");

            return $exitCode;
        } catch (Throwable $throwable) {
            $this->getExceptionHandler()->handle($throwable);
            return (int)($throwable->getCode() ?: self::DEFAULT_EXCEPTION_EXITCODE);
        }
    }

    public function __invoke(CollectionList $arguments): int
    {
        return $this->run($arguments);
    }

    abstract protected function execute(CollectionList $arguments): int;
}
