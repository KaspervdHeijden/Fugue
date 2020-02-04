<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Throwable;
use function error_reporting;
use function sprintf;

abstract class ErrorHandler implements ErrorHandlerInterface
{
    /** @var bool */
    private $registered = false;

    /**
     * Handle an UnhandledException.
     *
     * @param UnhandledErrorException $exception The unhandled exception
     * @return bool                              TRUE to continue, FALSE to exit immediately.
     */
    abstract protected function handle(UnhandledErrorException $exception): bool;

    protected function formatExceptionMessage(UnhandledErrorException $exception): string
    {
        return sprintf(
            "Unhandled exception caught by %s::%s.\n\nFile: %s:%d\nMessage: %s",
            static::class,
            __FUNCTION__,
            $exception->getFile(),
            (int)$exception->getLine(),
            $exception->getMessage()
        );
    }

    public function handleError(
        int $errorNumber,
        string $errorMessage,
        string $file,
        int $lineNumber
    ): void {
        try {
            if ((error_reporting() & $errorNumber) === 0) {
                return;
            }

            $unhandledException = UnhandledErrorException::create(
                $errorNumber,
                $errorMessage,
                $file,
                $lineNumber
            );

            if (! $this->handle($unhandledException)) {
                exit($errorNumber ?: 1);
            }
        } catch (Throwable $throwable) {
            exit($throwable->getTraceAsString());
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
