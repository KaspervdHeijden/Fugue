<?php

declare(strict_types=1);

namespace Fugue\Logging;

use Fugue\Core\Output\OutputHandlerInterface;

final class OutputLogger extends Logger
{
    /** @var OutputHandlerInterface */
    private $outputHandler;

    public function __construct(OutputHandlerInterface $outputHandler)
    {
        $this->outputHandler = $outputHandler;
    }

    public function verbose(string $message): void
    {
        $this->callLogIfNotEmpty(self::TYPE_VERBOSE, $message);
    }

    protected function log(string $logType, string $message): void
    {
        $this->outputHandler->writeLine($message);
    }
}
