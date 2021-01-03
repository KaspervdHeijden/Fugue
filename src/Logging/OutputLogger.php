<?php

declare(strict_types=1);

namespace Fugue\Logging;

use Fugue\Core\Output\OutputHandlerInterface;

final class OutputLogger extends Logger
{
    private OutputHandlerInterface $outputHandler;
    private bool $logVerbose;

    public function __construct(
        OutputHandlerInterface $outputHandler,
        bool $logVerbose
    ) {
        $this->outputHandler = $outputHandler;
        $this->logVerbose    = $logVerbose;
    }

    public function verbose(string $message): void
    {
        if ($this->logVerbose) {
            $this->callLogIfNotEmpty(self::TYPE_VERBOSE, $message);
        }
    }

    protected function log(string $logType, string $message): void
    {
        $this->outputHandler->write($message . PHP_EOL);
    }
}
