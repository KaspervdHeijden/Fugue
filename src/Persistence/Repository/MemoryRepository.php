<?php

declare(strict_types=1);

namespace Fugue\Persistence\Repository;

use Fugue\Collection\CollectionMap;

abstract class MemoryRepository
{
    private CollectionMap $map;

    public function __construct(?CollectionMap $map = null)
    {
        /** @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->map = ($map instanceof CollectionMap) ? $map : new CollectionMap();
    }

    protected function getMap(): CollectionMap
    {
        return $this->map;
    }
}
