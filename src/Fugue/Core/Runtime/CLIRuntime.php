<?php

declare(strict_types=1);

namespace Fugue\Core\Runtime;

use Fugue\Command\InvalidCommandException;
use Fugue\Collection\CollectionList;
use Fugue\Command\CommandFactory;
use Fugue\HTTP\Request;

final class CLIRuntime implements RuntimeInterface
{
    private CommandFactory $commandFactory;

    public function __construct(CommandFactory $commandFactory)
    {
        $this->commandFactory = $commandFactory;
    }

    public function handle(Request $request): void
    {
        $args = CollectionList::forMixed($request->server()->getArray('argv'));
        if ($args->count() < 2) {
            throw InvalidCommandException::forMissingIdentifier();
        }

        $command  = $this->commandFactory->getForIdentifier((string)$args[1]);
        $exitCode = $command->run($args->slice(2));

        exit($exitCode);
    }
}
