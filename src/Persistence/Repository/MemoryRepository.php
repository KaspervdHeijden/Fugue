<?php

declare(strict_types=1);

namespace Fugue\Persistence\Repository;

use Fugue\Collection\Map;

abstract class MemoryRepository
{
    /** @var Map */
    private $map;

    protected function getMap(): Map
    {
        if (! $this->map instanceof Map) {
            $this->map = new Map();
        }

        return $this->map;
    }
}
