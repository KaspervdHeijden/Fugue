<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_bool;

final class BooleanList extends ArrayList
{
    protected function checkValue($value): bool
    {
        return is_bool($value);
    }
}
