<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

final class UnhandledErrorException extends FugueException
{
    public static function create(
        int $errorNumber,
        string $errorMessage,
        string $fileName,
        int $lineNumber
    ): self {
        $exception       = new static($errorMessage, $errorNumber, null);
        $exception->line = $lineNumber;
        $exception->file = $fileName;

        return $exception;
    }
}
