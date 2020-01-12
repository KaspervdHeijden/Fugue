<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

final class QueryResult
{
    /** @var int */
    private $numberOfAffectedRows;

    /** @var string */
    private $lastInsertedId;

    public function __construct(int $numberOfAffectedRows, string $lastInsertedId)
    {
        $this->numberOfAffectedRows = $numberOfAffectedRows;
        $this->lastInsertedId       = $lastInsertedId;
    }

    public function getNumberOfAffectedRows(): int
    {
        return $this->numberOfAffectedRows;
    }

    public function getLastInsertedId(): string
    {
        return $this->lastInsertedId;
    }
}
