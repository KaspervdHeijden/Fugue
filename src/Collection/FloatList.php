<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function array_sum;
use function is_float;

final class FloatList extends ArrayList
{
    protected function checkValue($value): bool
    {
        return is_float($value);
    }

    public function sum(): float
    {
        return (float)array_sum($this->all());
    }
}
