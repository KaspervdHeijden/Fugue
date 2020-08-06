<?php

declare(strict_types=1);

namespace Fugue\Logging;

use DateTimeImmutable;

use function trim;

abstract class Logger implements LoggerInterface
{
    /** @var string */
    public const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';

    private string $dateFormat = self::DEFAULT_DATE_FORMAT;

    /** @var string */
    public const TYPE_WARNING = 'WARNING';

    /** @var string */
    public const TYPE_VERBOSE = 'VERBOSE';

    /** @var string */
    public const TYPE_ERROR   = 'ERROR';

    /** @var string */
    public const TYPE_INFO    = 'INFO';

    /**
     * Formats a message.
     *
     * @param string $logType The log type.
     * @param string $message The message to format.
     *
     * @return string         The formatted message.
     */
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

    /**
     * Gets the date format used in the log messages.
     *
     * @return string The date format used in log messages.
     */
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * Sets the date format used in the log messages.
     *
     * @param string The date format used in log messages.
     */
    public function setDateFormat(string $dateFormat): void
    {
        $this->dateFormat = $dateFormat;
    }

    /**
     * Logs a message.
     *
     * @param string $logType The log type.
     * @param string $message The message to log.
     */
    abstract protected function log(
        string $logType,
        string $message
    ): void;

    /**
     * Calls the log function after trimming the message.
     *
     * @param string $logType The log type.
     * @param string $message The message to log.
     */
    protected function callLogIfNotEmpty(
        string $logType,
        string $message
    ): void {
        $msg = trim($message);
        if ($msg !== '') {
            $this->log($logType, $msg);
        }
    }

    /**
     * Logs an error message.
     *
     * @param string $message The message to log.
     */
    public function error(string $message): void
    {
        $this->callLogIfNotEmpty(self::TYPE_ERROR, $message);
    }

    /**
     * Logs an informational message.
     *
     * @param string $message The message to log.
     */
    public function info(string $message): void
    {
        $this->callLogIfNotEmpty(self::TYPE_INFO, $message);
    }

    /**
     * Logs a warning message.
     *
     * @param string $message The message to log.
     */
    public function warning(string $message): void
    {
        $this->callLogIfNotEmpty(self::TYPE_WARNING, $message);
    }

    /**
     * Logs an verbose message.
     *
     * @param string $message The message to log.
     */
    public function verbose(string $message): void
    {
    }
}
