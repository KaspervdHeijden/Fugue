<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Throwable;

final class EmptyExceptionHandler implements ExceptionHandlerInterface
{
    public function handle(Throwable $throwable): void
    {
    }
}
