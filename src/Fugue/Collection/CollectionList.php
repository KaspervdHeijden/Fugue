<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_int;

class CollectionList extends Collection
{
    protected function checkKey(mixed $key): bool
    {
        if (! parent::checkKey($key)) {
            return false;
        }

        if ($key === null) {
            return true;
        }

        if (is_int($key)) {
            return true;
        }

        return false;
    }
}
