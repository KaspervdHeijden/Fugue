<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Throwable;

use function sprintf;

abstract class ExceptionHandler implements ExceptionHandlerInterface
{
    protected function formatExceptionMessage(Throwable $throwable): string
    {
        return sprintf(
            "Unhandled exception caught by %s::%s.\n\nFile: %s:%d\nMessage: %s\n",
            static::class,
            __FUNCTION__,
            $throwable->getFile(),
            (int)$throwable->getLine(),
            $throwable->getMessage()
        );
    }
}
