<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

interface DatabaseQueryAdapterInterface
{
    public function query(
        string $sql,
        array $params = []
    ): QueryResult;

    /**
     * Fetches one record in to a class.
     *
     * @param string      $sql       The SQL query to perform.
     * @param array       $params    Replaceable parameters.
     * @param string|null $className The name of the class to load the results in.
     *
     * @return object|null           An instance of $className, or NULL if not found.
     */
    public function fetchOne(
        string $sql,
        array $params = [],
        ?string $className = null
    ): ?object;

    /**
     * Fetches one record in to a class.
     *
     * @param string      $sql       The SQL query to perform.
     * @param array       $params    Replaceable parameters.
     * @param string|null $className The name of the class to load the results in.
     *
     * @return object[]              An array of instances of $className.
     */
    public function fetchAll(
        string $sql,
        array $params = [],
        ?string $className = null
    ): array;

    /**
     * Fetches the first column of the first record.
     *
     * @param string $sql           The SQL query to perform.
     * @param array  $params        Replaceable parameters.
     *
     * @return float|int|bool|string|null The value of the first column of the first row.
     */
    public function fetchValue(
        string $sql,
        array $params = []
    ): float|int|bool|string|null;
}
