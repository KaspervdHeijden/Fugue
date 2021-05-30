<?php

declare(strict_types=1);

namespace Fugue\Logging;

use DateTimeImmutable;

use function trim;

abstract class Logger implements LoggerInterface
{
    public const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';

    public const TYPE_WARNING = 'WARNING';
    public const TYPE_VERBOSE = 'VERBOSE';
    public const TYPE_ERROR   = 'ERROR';
    public const TYPE_INFO    = 'INFO';

    private string $dateFormat = self::DEFAULT_DATE_FORMAT;

    protected function getFormattedMessage(
        string $logType,
        string $message
    ): string {
        $formattedLogType = ($logType) ? "[{$logType}]: " : '';
        $now              = new DateTimeImmutable();

        return $now->format($this->getDateFormat()) .
                " {$formattedLogType}{$message}" .
                PHP_EOL;
    }

    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public function setDateFormat(string $dateFormat): void
    {
        $this->dateFormat = $dateFormat;
    }

    abstract protected function log(string $logType, string $message): void;

    protected function callLogIfNotEmpty(
        string $logType,
        string $message
    ): void {
        $msg = trim($message);
        if ($msg !== '') {
            $this->log($logType, $msg);
        }
    }

    public function error(string $message): void
    {
        $this->callLogIfNotEmpty(self::TYPE_ERROR, $message);
    }

    public function info(string $message): void
    {
        $this->callLogIfNotEmpty(self::TYPE_INFO, $message);
    }

    public function warning(string $message): void
    {
        $this->callLogIfNotEmpty(self::TYPE_WARNING, $message);
    }

    public function verbose(string $message): void
    {
        $this->callLogIfNotEmpty(self::TYPE_VERBOSE, $message);
    }
}
