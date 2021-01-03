<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Core\Exception\FugueException;

final class InvalidClassException extends FugueException
{
    public static function forClassName(string $className): self
    {
        return new self("Could not load class {$className}");
    }
}
