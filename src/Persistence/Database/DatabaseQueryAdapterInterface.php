<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

/**
 * A generic database query adapter.
 */
interface DatabaseQueryAdapterInterface
{
    /**
     * Performs a query on the database.
     *
     * @param string $sql       The SQL query to perform.
     * @param array  $params    Replaceable parameters.
     *
     * @return QueryResult      The result of the query.
     */
    public function query(string $sql, array $params = []): QueryResult;

    /**
     * Fetches one record in to a class.
     *
     * @param string $className The name of the class to load the results in.
     * @param string $sql       The SQL query to perform.
     * @param array  $params    Replaceable parameters.
     *
     * @return object|null Instance of $className, or NULL if not found.
     */
    public function fetchOne(string $className, string $sql, array $params = []);

    /**
     * Fetches one record in to a class.
     *
     * @param string $className The name of the class to load the results in.
     * @param string $sql       The SQL query to perform.
     * @param array  $params    Replaceable parameters.
     *
     * @return object|null Instance of $className, or NULL if not found.
     */
    public function fetchAll(string $className, string $sql, array $params = []);

    /**
     * Fetches the first column of the first record.
     *
     * @param string $sql           The SQL query to perform.
     * @param array  $params        Replaceable parameters.
     *
     * @return int|bool|string|null The value of the first column of the first row.
     */
    public function fetchValue(string $sql, array $params = []);
}
