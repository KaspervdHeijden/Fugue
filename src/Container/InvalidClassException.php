<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Core\Exception\FugueException;
use Throwable;

final class InvalidClassException extends FugueException
{
    public static function forClassName(
        string $className,
        ?Throwable $innerException
    ): self {
        $message = "Could not load class {$className}";
        if (
            $innerException instanceof Throwable &&
            $innerException->getMessage() !== ''
        ) {
            $message .= " ({$innerException->getMessage()})";
        }

        return new self($message);
    }
}
