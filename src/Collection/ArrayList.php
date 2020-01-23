<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_int;

class ArrayList extends CustomArray
{
    protected function checkKey($key): bool
    {
        if (! is_int($key)) {
            return false;
        }

        return true;
    }
}
