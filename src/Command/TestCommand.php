<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Logging\LoggerInterface;

final class TestCommand implements CommandInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function run(array $arguments): void
    {
        $this->logger->info('OK');
    }
}
