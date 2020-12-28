<?php

declare(strict_types=1);

namespace Fugue\Core\Exception;

use Throwable;

interface ExceptionHandlerInterface
{
    public function handle(Throwable $throwable): void;
}
