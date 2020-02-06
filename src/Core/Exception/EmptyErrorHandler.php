<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

final class EmptyErrorHandler extends ErrorHandler
{
    protected function handle(UnhandledErrorException $exception): bool
    {
        return false;
    }
}
