<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Logging\LoggerInterface;
use Throwable;

abstract class Command implements CommandInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $name;

    final public function __construct(LoggerInterface $logger)
    {
        $this->name   = (string)array_slice(explode('\\', static::class), -1)[0];
        $this->logger = $logger;
    }

    protected function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    protected function getName(): string
    {
        return $this->name;
    }

    public function run(array $arguments): void
    {
        try {
            $this->logger->verbose("Starting command {$this->name}");
            $this->execute($arguments);
            $this->logger->verbose("Completed command {$this->name}");
        } catch (Throwable $throwable) {
            $this->logger->error(
                "Exception during command {$this->name}: {$throwable->getMessage()}"
            );
        }
    }

    /**
     * Method containing the logic to perform when running a Command.
     *
     * @param string[] $arguments The arguments passed.
     */
    abstract protected function execute(array $arguments): void;
}
