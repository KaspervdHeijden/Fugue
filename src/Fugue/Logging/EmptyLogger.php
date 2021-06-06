<?php

declare(strict_types=1);

namespace Fugue\Logging;

final class EmptyLogger extends Logger
{
    protected function log(string $logType, string $message): void
    {
    }
}
