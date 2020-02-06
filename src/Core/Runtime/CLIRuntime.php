<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\Command\InvalidCommandException;
use Fugue\Logging\LoggerInterface;
use Fugue\Command\CommandFactory;
use Fugue\HTTP\Request;

use function array_slice;
use function count;

final class CLIRuntime implements RuntimeInterface
{
    /** @var LoggerInterface */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function handle(Request $request): void
    {
        $argv = $request->server()->getArray('argv');
        if (count($argv) < 2) {
            throw InvalidCommandException::forMissingIdentifier();
        }

        $factory = new CommandFactory($this->logger);
        $command = $factory->getForIdentifier((string)$argv[1]);

        $command->run(array_slice($argv, 2));
    }
}
