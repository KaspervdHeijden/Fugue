<?php

declare(strict_types=1);

namespace Fugue\Logging;

use function trim;

final class MemoryLogger extends Logger
{
    /** @var string[] */
    private array $logs = [];

    /**
     * Gets all logs.
     *
     * @return string[] All logged messages.
     */
    public function getLogs(): array
    {
        return $this->logs;
    }

    /**
     * Clears the log.
     */
    public function clear(): void
    {
        $this->logs = [];
    }

    protected function log(string $logType, string $message): void
    {
        $this->logs[] = trim($this->getFormattedMessage($logType, $message));
    }
}
