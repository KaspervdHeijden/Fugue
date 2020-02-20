<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Throwable;

interface ExceptionHandlerInterface
{
    /**
     * Handles an Exception.
     *
     * @param Throwable The exception to handle.
     */
    public function handle(Throwable $throwable): void;
}
