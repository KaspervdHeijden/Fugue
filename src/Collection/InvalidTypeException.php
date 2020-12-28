<?php

declare(strict_types=1);

namespace Fugue\Collection;

use UnexpectedValueException;

final class InvalidTypeException extends UnexpectedValueException
{
    public static function forKey(string $className): self
    {
        return new InvalidTypeException("Invalid key type for {$className}.");
    }

    public static function forValue(string $className): self
    {
        return new InvalidTypeException("Invalid value type for {$className}.");
    }
}
