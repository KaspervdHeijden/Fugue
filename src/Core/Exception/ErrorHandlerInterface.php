<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

interface ErrorHandlerInterface
{
    /**
     * Handles an Exception.
     *
     * @param int    $errorNumber  The error code.
     * @param string $errorMessage The error message.
     * @param string $file         The filename where the error occurred.
     * @param int    $lineNumber   The line number at which the error occurred.
     */
    public function handleError(
        int $errorNumber,
        string $errorMessage,
        string $file,
        int $lineNumber
    ): void;

    public function register(): void;
}
