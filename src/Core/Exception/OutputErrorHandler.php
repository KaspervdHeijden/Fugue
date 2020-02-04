<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Fugue\Core\Output\OutputHandlerInterface;

final class OutputErrorHandler extends ErrorHandler
{
    /** @var OutputHandlerInterface */
    private $output;

    public function __construct(OutputHandlerInterface $outputHandler)
    {
        $this->output = $outputHandler;
    }

    protected function handle(UnhandledErrorException $exception): bool
    {
        $this->output->write($this->formatExceptionMessage($exception));
        return false;
    }
}
