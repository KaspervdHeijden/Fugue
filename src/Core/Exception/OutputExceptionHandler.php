<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Fugue\Core\Output\OutputHandlerInterface;
use Throwable;

final class OutputExceptionHandler extends ExceptionHandler
{
    /** @var OutputHandlerInterface */
    private $output;

    public function __construct(OutputHandlerInterface $outputHandler)
    {
        $this->output = $outputHandler;
    }

    public function handle(Throwable $exception): void
    {
        $this->output->write($this->formatExceptionMessage($exception));
    }
}
