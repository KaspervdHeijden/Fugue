<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Throwable;

interface ExceptionHandlerInterface
{
    /**
     * Handles an Exception.
     */
    public function handle(Throwable $throwable): void;
}
