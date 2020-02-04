<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Fugue\Core\Output\OutputHandlerInterface;

use function error_reporting;
use function sprintf;

final class OutputErrorHandler implements ErrorHandlerInterface
{
    /** @var OutputHandlerInterface */
    private $outputHandler;

    /** @var bool */
    private $exitOnError;

    /** @var bool */
    private $registered;

    public function __construct(
        OutputHandlerInterface $outputHandler,
        bool $exitOnError
    ) {
        $this->outputHandler = $outputHandler;
        $this->exitOnError   = $exitOnError;
        $this->registered    = false;
    }

    public function handleError(
        int $errorNumber,
        string $errorMessage,
        string $file,
        int $lineNumber
    ): void {
        if ((error_reporting() & $errorNumber) === 0) {
            return;
        }

        $this->outputHandler->write(sprintf(
            "Exception caught by %s::%s.\n\nFile: %s:%d\nMessage: %s",
            static::class,
            __FUNCTION__,
            $file,
            $lineNumber,
            $errorMessage
        ));

        if ($this->exitOnError) {
            exit($errorNumber ?: 1);
        }
    }

    public function register(): void
    {
        if ($this->registered) {
            return;
        }

        set_error_handler([$this, 'handleError']);
        $this->registered = true;
    }
}
