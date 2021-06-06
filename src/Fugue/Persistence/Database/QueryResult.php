<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

final class QueryResult
{
    private ?string $insertedId;
    private int $affectedRows;

    public function __construct(int $affectedRows, ?string $insertedId)
    {
        $this->affectedRows = $affectedRows;
        $this->insertedId   = $insertedId;
    }

    public function getAffectedRows(): int
    {
        return $this->affectedRows;
    }

    public function getInsertedId(): ?string
    {
        return $this->insertedId;
    }
}
