<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

final class EmptyDatabaseQueryAdapter implements DatabaseQueryAdapterInterface
{
    public function fetchOne(
        string $className,
        string $sql,
        array $params = []
    ) {
        return null;
    }

    public function fetchAll(
        string $className,
        string $sql,
        array $params = []
    ): array {
        return [];
    }

    public function fetchValue(
        string $sql,
        array $params = []
    ) {
        return null;
    }

    public function query(
        string $sql,
        array $params = []
    ): QueryResult {
        return new QueryResult(0, null);
    }
}
