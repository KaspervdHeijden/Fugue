<?php

declare(strict_types=1);

namespace Fugue\Logging;

interface LoggerInterface
{
    /**
     * Logs a informational message.
     *
     * @param string $message The message to log.
     */
    public function info(string $message): void;

    /**
     * Logs a warning message.
     *
     * @param string $message The message to log.
     */
    public function warning(string $message): void;

    /**
     * Logs an error message.
     *
     * @param string $message The message to log.
     */
    public function error(string $message): void;

    /**
     * Logs a verbose message.
     *
     * @param string $message The message to log.
     */
    public function verbose(string $message): void;
}
