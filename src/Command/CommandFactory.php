<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Logging\LoggerInterface;

final class CommandFactory
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getForIdentifier(string $identifier): CommandInterface
    {
        throw InvalidCommandException::forUnknownIdentifier($identifier);
    }
}
