<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Collection\CollectionList;

final class TestCommand extends Command
{
    public function execute(CollectionList $arguments): int
    {
        $this->getLogger()->info('Executing TestCommand');

        return 0;
    }
}
