<?php

declare(strict_types=1);

namespace Fugue\Logging;

use InvalidArgumentException;

final class LoggerFactory
{
    /**
     * @var string Empty logger identifier.
     */
    public const LOGGER_EMPTY = 'empty';

    /**
     * @var string File logger identifier.
     */
    public const LOGGER_FILE = 'file';

    /**
     * @var string Memory logger identifier.
     */
    public const LOGGER_MEMORY = 'memory';

    /**
     * @var string Multi logger identifier.
     */
    public const LOGGER_MULTI = 'multi';

    /**
     * @var string Output logger identifier.
     */
    public const LOGGER_STDOUT = 'stdout';

    /**
     * Gets a LoggerInterface from an identifier.
     *
     * @param string $identifier The identifier to get the LoggerInterface for.
     * @return LoggerInterface   The LoggerInterface.
     */
    public function getLoggerFromIdentifier(string $identifier): LoggerInterface
    {
        switch ($identifier) {
            case self::LOGGER_EMPTY:
                return new EmptyLogger();
            case self::LOGGER_FILE:
                return new FileLogger();
            case self::LOGGER_MULTI:
                return new MultiLogger();
            case self::LOGGER_MEMORY:
                return new MemoryLogger();
            case self::LOGGER_STDOUT:
                return new OutputLogger();
            default:
                throw new InvalidArgumentException(
                    "Identifier {$identifier} not recognized."
                );
        }
    }
}
