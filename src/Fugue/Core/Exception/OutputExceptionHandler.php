<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Fugue\Core\Output\OutputHandlerInterface;
use Throwable;

final class OutputExceptionHandler extends ExceptionHandler
{
    private OutputHandlerInterface $outputHandler;

    public function __construct(OutputHandlerInterface $outputHandler)
    {
        $this->outputHandler = $outputHandler;
    }

    public function handle(Throwable $throwable): void
    {
        $message = $this->formatExceptionMessage($throwable);
        $this->outputHandler->write($message);
    }
}
