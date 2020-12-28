<?php

declare(strict_types=1);

namespace Fugue\Command;

use Fugue\Collection\CollectionList;

interface CommandInterface
{
    public function run(CollectionList $arguments): int;
}
