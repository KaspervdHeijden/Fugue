<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_string;

final class StringList extends ArrayList
{
    protected function checkValue($value): bool
    {
        return is_string($value);
    }
}
