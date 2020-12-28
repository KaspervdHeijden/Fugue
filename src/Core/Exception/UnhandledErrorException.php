<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Throwable;

final class UnhandledErrorException extends FugueException
{
    public static function create(
        int $code,
        string $message,
        string $file,
        int $line,
        Throwable $previous = null
    ): self {
        $exception = new UnhandledErrorException(
            $message,
            $code,
            $previous
        );

        $exception->line = $line;
        $exception->file = $file;

        return $exception;
    }
}
