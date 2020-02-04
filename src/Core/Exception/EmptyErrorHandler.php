<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

final class EmptyErrorHandler implements ErrorHandlerInterface
{
    public function handleError(
        int $errorNumber,
        string $errorMessage,
        string $file,
        int $lineNumber
    ): void {
    }

    public function register(): void
    {
    }
}
