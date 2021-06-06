<?php

declare(strict_types=1);

namespace Fugue\Logging;

use function trim;

final class MemoryLogger extends Logger
{
    /** @var string[] */
    private array $logs = [];

    public function getLogs(): array
    {
        return $this->logs;
    }

    public function clear(): void
    {
        $this->logs = [];
    }

    protected function log(string $logType, string $message): void
    {
        $logMessage = trim($this->getFormattedMessage($logType, $message));
        if ($logMessage !== '') {
            $this->logs[] = $logMessage;
        }
    }
}
