<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\Command\InvalidCommandException;
use Fugue\Command\CommandFactory;
use Fugue\HTTP\Request;

use function array_slice;
use function count;

final class CLIRuntime implements RuntimeInterface
{
    private CommandFactory $commandFactory;

    public function __construct(CommandFactory $commandFactory)
    {
        $this->commandFactory = $commandFactory;
    }

    public function handle(Request $request): void
    {
        $argv = $request->server()->getArray('argv');
        if (count($argv) < 2) {
            throw InvalidCommandException::forMissingIdentifier();
        }

        $command  = $this->commandFactory->getForIdentifier((string)$argv[1]);
        $exitCode = $command->run(array_slice($argv, 2));

        if ($exitCode !== 0) {
            exit($exitCode);
        }
    }
}
