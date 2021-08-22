<?php

declare(strict_types=1);

namespace Fugue\Collection;

use function array_slice;
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

    public function slice(int $offset, ?int $length = null): static
    {
        return $this->cloneType(
            array_slice(
                $this->toArray(),
                $offset,
                $length,
                true
            )
        );
    }

    public function subset(int $start, ?int $end = null): static
    {
        return $this->slice(
            $start,
            $end === null ? null : ($end - $start)
        );
    }
}
