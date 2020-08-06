<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Core\Exception\FugueException;

final class InvalidCommandException extends FugueException
{
    public static function forMissingIdentifier(): self
    {
        return new self('No command identifier given.');
    }

    public static function forUnknownIdentifier(
        string $identifier
    ): self {
        return new self(
            "Command identifier not recognized '{$identifier}'."
        );
    }

    public static function forInvalidClassType(string $identifier): self
    {
        return new self(
            "Object instance for identifier '{$identifier}' does not implement CommandInterface."
        );
    }
}
