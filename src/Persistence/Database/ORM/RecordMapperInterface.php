<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database\ORM;

interface RecordMapperInterface
{
    /**
     * Instantiates an object for a record.
     *
     * @return object
     */
    public function arrayToObject(array $record): object;
}
