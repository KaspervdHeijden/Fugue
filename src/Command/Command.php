<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Core\Exception\ExceptionHandlerInterface;
use Fugue\Logging\LoggerInterface;
use Throwable;

use function array_slice;
use function explode;

abstract class Command implements CommandInterface
{
    /** @var ExceptionHandlerInterface */
    private $exceptionHandler;

    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $name;

    final public function __construct(
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

    public function run(array $arguments): void
    {
        try {
            $this->logger->verbose("Starting {$this->name}");
            $this->execute($arguments);
            $this->logger->verbose("Completed {$this->name}");
        } catch (Throwable $throwable) {
            $this->logger->error("Exception during {$this->name}: {$throwable->getMessage()}");
            $this->exceptionHandler->handle($throwable);
        }
    }

    /**
     * Method containing the logic to perform when running a command.
     *
     * @param string[] $arguments The arguments passed.
     */
    abstract protected function execute(array $arguments): void;
}
