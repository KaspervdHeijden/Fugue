<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Fugue\Core\Output\OutputHandlerInterface;
use Throwable;

final class OutputExceptionHandler extends ExceptionHandler
{
    private OutputHandlerInterface $output;

    public function __construct(OutputHandlerInterface $outputHandler)
    {
        $this->output = $outputHandler;
    }

    public function handle(Throwable $throwable): void
    {
        $message = $this->formatExceptionMessage($throwable);
        $this->output->write($message);
    }
}
