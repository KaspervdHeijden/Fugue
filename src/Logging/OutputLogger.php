<?php

declare(strict_types=1);

namespace Fugue\Logging;

use Fugue\Core\Output\OutputHandlerInterface;

final class OutputLogger extends Logger
{
    private OutputHandlerInterface $outputHandler;

    public function __construct(OutputHandlerInterface $outputHandler)
    {
        $this->outputHandler = $outputHandler;
    }

    protected function log(string $logType, string $message): void
    {
        $this->outputHandler->write($message . PHP_EOL);
    }
}
