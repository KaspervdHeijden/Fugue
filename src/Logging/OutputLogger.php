<?php

declare(strict_types=1);

namespace Fugue\Logging;

final class OutputLogger extends Logger
{
    public function verbose(string $message): void
    {
        $this->callLogIfNotEmpty(self::TYPE_VERBOSE, $message);
    }

    protected function log(string $logType, string $message): void
    {
        echo $message . PHP_EOL;
    }
}
