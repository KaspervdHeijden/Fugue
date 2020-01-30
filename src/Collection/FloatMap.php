<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function array_sum;
use function is_float;

final class FloatMap extends ArrayMap
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
