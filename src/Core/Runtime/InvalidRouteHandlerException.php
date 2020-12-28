<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\Core\Exception\FugueException;

final class InvalidRouteHandlerException extends FugueException
{
    public static function nonExistentClass(string $className): self
    {
        if ($className === '') {
            return new InvalidRouteHandlerException(
                'Cannot load empty class.'
            );
        }

        return new InvalidRouteHandlerException(
            "Cannot load class '{$className}'."
        );
    }

    public static function nonExistentClassFunction(
        string $className,
        string $methodName
    ): self {
        return new InvalidRouteHandlerException(
            "Handler function does not exist: '{$className}->{$methodName}()'."
        );
    }
}
