<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database\ORM;

interface RecordMapperInterface
{
    public function arrayToObject(array $record): object;
}
