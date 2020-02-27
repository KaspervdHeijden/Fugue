<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

final class QueryResult
{
    /** @var int */
    private $numberOfAffectedRows;

    /** @var string|null */
    private $insertedId;

    public function __construct(int $numberOfAffectedRows, ?string $insertedId)
    {
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
