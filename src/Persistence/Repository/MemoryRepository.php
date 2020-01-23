<?php

declare(strict_types=1);

namespace Fugue\Persistence\Repository;

use Fugue\Collection\ArrayMap;

abstract class MemoryRepository
{
    /** @var ArrayMap */
    private $map;

    protected function getMap(): ArrayMap
    {
        if (! $this->map instanceof ArrayMap) {
            $this->map = new ArrayMap();
        }

        return $this->map;
    }
}
