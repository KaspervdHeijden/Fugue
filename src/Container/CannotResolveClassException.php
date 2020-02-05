<?php

declare(strict_types=1);

namespace Fugue\Container;

use LogicException;

final class CannotResolveClassException extends LogicException
{
    public static function forConstructorParameter(string $parameter, string $className): self
    {
        return new self(
            "Could not resolve argument {$parameter} for constructor of class {$className}."
        );
    }

    public static function forUnresolvedClass(string $className): self
    {
        return new self("Could not resolve class {$className}.");
    }
}
