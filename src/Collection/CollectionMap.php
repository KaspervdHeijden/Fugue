<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_string;
use function is_int;

class CollectionMap extends Collection
{
    protected function checkKey($key): bool
    {
        if (! parent::checkKey($key)) {
            return false;
        }

        if (! is_string($key) && ! is_int($key)) {
            return false;
        }

        return true;
    }
}
