<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database\ORM;

final class StdClassMapper implements RecordMapperInterface
{
    public function arrayToObject(array $record): object
    {
        return (object)$record;
    }
}
