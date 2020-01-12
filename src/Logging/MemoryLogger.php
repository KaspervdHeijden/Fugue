<?php

declare(strict_types=1);

namespace Fugue\Logging;

use function trim;

final class MemoryLogger extends Logger
{
    /** @var string[] */
    private $logs = [];

    /**
     * Gets all logs.
     *
     * @return string[] The log messages logged so far.
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Clears all logs.
     */
    public function clearLogs(): void
    {
        $this->logs = [];
    }

    protected function log(string $logType, string $message): void
    {
        $this->logs[] = trim($this->getFormattedMessage($logType, $message));
    }
}
