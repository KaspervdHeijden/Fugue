<?php

declare(strict_types=1);

namespace Fugue\Persistence\Repository;

use Fugue\Collection\CollectionMap;

abstract class MemoryRepository
{
    /** @var CollectionMap */
    private $map;

    protected function getMap(): CollectionMap
    {
        if (! $this->map instanceof CollectionMap) {
            $this->map = new CollectionMap();
        }

        return $this->map;
    }
}
