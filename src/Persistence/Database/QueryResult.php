<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

final class QueryResult
{
    private int $numberOfAffectedRows;

    private ?string $insertedId;

    public function __construct(
        int $numberOfAffectedRows,
        ?string $insertedId
    ) {
        $this->numberOfAffectedRows = $numberOfAffectedRows;
        $this->insertedId           = $insertedId;
    }

    public function getNumberOfAffectedRows(): int
    {
        return $this->numberOfAffectedRows;
    }

    public function getInsertedId(): ?string
    {
        return $this->insertedId;
    }
}
