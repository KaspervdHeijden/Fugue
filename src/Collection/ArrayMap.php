<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_string;
use function is_int;

class ArrayMap extends CustomArray
{
    /**
     * @param string|int|null $key The key to check.
     * @return bool        TRUE if the value is OK, FALSE otherwise.
     */
    protected function checkKey($key): bool
    {
        if (! is_string($key) && ! is_int($key)) {
            return false;
        }

        return true;
    }
}
