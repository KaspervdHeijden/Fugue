<?php

declare(strict_types=1);

namespace Fugue\Command;

interface CommandInterface
{
    /**
     * Executes a command.
     *
     * @param string[] $arguments
     */
    public function run(array $arguments): int;
}
