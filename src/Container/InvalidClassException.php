<?php

declare(strict_types=1);

namespace Fugue\Container;

use LogicException;

final class InvalidClassException extends LogicException
{
    public static function forClassName(string $className): self
    {
        return new self("Could not load class {$className}");
    }
}
