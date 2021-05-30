<?php

declare(strict_types=1);

namespace Fugue\Collection;

class CollectionSet extends Collection
{
    protected function checkValue(mixed $value): bool
    {
        if (! parent::checkValue($value)) {
            return false;
        }

        if ($this->contains($value)) {
            return false;
        }

        return true;
    }
}
