<?php

declare(strict_types=1);

namespace Fugue\Persistence\Repository;

use Fugue\Collection\CollectionMap;

abstract class MemoryRepository
{
    /** @var CollectionMap */
    private $map;

    public function __construct(?CollectionMap $map = null)
    {
        $this->map = ($map instanceof CollectionMap) ? $map : new CollectionMap();
    }

    protected function getMap(): CollectionMap
    {
        return $this->map;
    }
}
