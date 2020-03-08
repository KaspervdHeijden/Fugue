<?php

declare(strict_types=1);

namespace Fugue\Mailing\Recipient;

use Fugue\Collection\CollectionList;

final class RecipientList extends CollectionList
{
    protected function checkValue($value): bool
    {
        return $value instanceof Recipient;
    }
}
