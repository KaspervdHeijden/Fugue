<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_int;

class CollectionList extends Collection
{
    protected function checkKey($key): bool
    {
        if (! parent::checkKey($key)) {
            return false;
        }

        if ($key !== null && ! is_int($key)) {
            return false;
        }

        return true;
    }
}
