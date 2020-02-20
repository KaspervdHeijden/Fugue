<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Core\Exception\ExceptionHandlerInterface;
use Fugue\Logging\LoggerInterface;

final class CommandFactory
{
    /** @var ExceptionHandlerInterface */
    private $exceptionHandler;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ExceptionHandlerInterface $exceptionHandler,
        LoggerInterface $logger
    ) {
        $this->exceptionHandler = $exceptionHandler;
        $this->logger           = $logger;
    }

    public function getForIdentifier(string $identifier): CommandInterface
    {
        throw InvalidCommandException::forUnknownIdentifier($identifier);
    }
}
