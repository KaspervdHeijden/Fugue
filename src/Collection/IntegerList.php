<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function array_sum;
use function is_int;

final class IntegerList extends ArrayList
{
    protected function checkValue($value): bool
    {
        return is_int($value);
    }

    public function sum(): int
    {
        return (int)array_sum($this->all());
    }
}
