<?php

declare(strict_types=1);

namespace Fugue\Logging;

use function array_search;
use function is_int;

final class MultiLogger extends Logger
{
    /** @var LoggerInterface[] */
    private $loggers = [];

    public function __construct(LoggerInterface ...$loggers)
    {
        $this->loggers = $loggers;
    }

    private function getLoggerIndex(LoggerInterface $logger): ?int
    {
        $index = array_search($logger, $this->loggers, true);
        if (is_int($index)) {
            return (int)$index;
        }

        return null;
    }

    /**
     * Adds a logger endpoint to this MultiLogger.
     *
     * @param LoggerInterface $logger The logger to add.
     */
    public function addLogger(LoggerInterface $logger): void
    {
        if ($this->getLoggerIndex($logger) === null) {
            $this->loggers[] = $logger;
        }
    }

    /**
     * Gets all loggers attached to this MultiLogger.
     *
     * @return LoggerInterface[] The loggers attached to this MultiLogger.
     */
    public function getLoggers(): array
    {
        return $this->loggers;
    }

    /**
     * Removes a logger endpoint.
     *
     * @param LoggerInterface $logger The logger to remove.
     */
    public function removeLogger(LoggerInterface $logger): void
    {
        $index = $this->getLoggerIndex($logger);
        if (is_int($index)) {
            unset($this->loggers[$index]);
        }
    }

    protected function log(string $logType, string $message): void
    {
        foreach ($this->loggers as $logger) {
            $logger->log($logType, $message);
        }
    }
}
