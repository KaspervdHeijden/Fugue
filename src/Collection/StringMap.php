<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function is_string;

final class StringMap extends ArrayMap
{
    protected function checkValue($value): bool
    {
        return is_string($value);
    }
}
