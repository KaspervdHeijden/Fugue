<?php

declare(strict_types=1);

namespace Fugue\Persistence\Repository;

use Fugue\Collection\CollectionMap;

abstract class MemoryRepository
{
    /** @var CollectionMap */
    private $map;

    public function __construct(CollectionMap $map)
    {
        $this->map = $map;
    }

    protected function getMap(): CollectionMap
    {
        return $this->map;
    }
}
