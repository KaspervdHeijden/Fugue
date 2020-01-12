<?php

declare(strict_types=1);

namespace Fugue\CronJob;

use Throwable;
use Fugue\Logging\LoggerInterface;

abstract class CronJob implements CronJobInterface
{
    /** @var LoggerInterface */
    private $logger;

    /** @var string[] */
    private $arguments;

    /**
     * Instantiates a CronJob.
     *
     * @param string[]        $arguments The arguments for this CronJob.
     * @param LoggerInterface $logger    A logger.
     */
    final public function __construct(array $arguments, LoggerInterface $logger)
    {
        $this->arguments = $arguments;
        $this->logger    = $logger;
    }

    /**
     * Gets the name of this CronJob.
     *
     * @return string The name of this CronJob.
     */
    protected function getCronName(): string
    {
        return (string)array_slice(explode('\\', static::class), -1)[0];
    }

    /**
     * Logs an error message.
     *
     * @param string $message The error message.
     */
    final protected function logError(string $message): void
    {
        $this->logger->error($message);
    }

    /**
     * Logs an informational message.
     *
     * @param string $message The informational message.
     */
    final protected function logInfo(string $message): void
    {
        $this->logger->info($message);
    }

    /**
     * Logs a warning message.
     *
     * @param string $message The warning message.
     */
    final protected function logWarning(string $message): void
    {
        $this->logger->warning($message);
    }

    /**
     * Gets the arguments passed from the CLI.
     *
     * @return string[] The arguments.
     */
    protected function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Gets a value if this CronJob is active.
     *
     * @return TRUE if this CronJob is active, and should be executed, FALSE otherwise.
     */
    protected function isActive(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run(): void
    {
        if (! $this->isActive()) {
            return;
        }

        $cronName = $this->getCronName();
        $this->logInfo("Starting cronjob {$cronName}");
        try {
            $this->execute($this->getArguments());
            $this->logInfo("Completed cronjob {$cronName}");
        } catch (Throwable $throwable) {
            $this->logError("Exception during cronjob {$cronName}: {$throwable->getMessage()}");
        }
    }

    /**
     * Method containing the logic to perform when running a CronJob.
     *
     * @param string[] $arguments The arguments passed.
     */
    abstract protected function execute(array $arguments): void;
}
