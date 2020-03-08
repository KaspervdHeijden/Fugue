<?php

declare(strict_types=1);

namespace Fugue\Command;

final class TestCommand extends Command
{
    protected function execute(array $arguments): void
    {
        $this->getLogger()->info('OK');
    }
}
