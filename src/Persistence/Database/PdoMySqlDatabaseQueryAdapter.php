<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

use Fugue\Logging\LoggerInterface;
use RuntimeException;
use PDOStatement;
use PDOException;
use PDO;

use function is_resource;
use function array_map;
use function is_string;
use function is_array;
use function is_null;
use function is_bool;
use function is_int;

final class PdoMySqlDatabaseQueryAdapter implements DatabaseQueryAdapterInterface
{
    /** @var DatabaseConnectionSettings */
    private $settings;

    /** @var LoggerInterface */
    private $logger;

    /** @var PDO */
    private $pdo;

    public function __construct(
        DatabaseConnectionSettings $settings,
        LoggerInterface $logger
    ) {
        $this->settings = $settings;
        $this->logger   = $logger;
    }

    private function connect(): void
    {
        if ($this->pdo instanceof PDO) {
            return;
        }

        $this->logger->info('Connecting to database');
        $this->pdo = new PDO(
            $this->settings->getDsn(),
            $this->settings->getUser(),
            $this->settings->getPassword(),
            $this->settings->getOptions()
        );

        if ($this->settings->getCharset() !== '') {
            $this->pdo->query("SET NAMES '{$this->settings->getCharset()}'");
        }

        $timeZone = $this->settings->getTimezone();
        if ($timeZone !== '') {
            if (
                ! $this->pdo->query('SET @session.time_zone = ?', [$timeZone]) &&
                ! $this->pdo->query('SET @@time_zone = ?', [$timeZone])
            ) {
                throw new RuntimeException('Could not set time zone.');
            }
        }
    }

    /**
     * Throws an exception based on the array input.
     *
     * @param array $errorInfo The error info of the last occurred error.
     */
    private function throwExceptionFromArray(array $errorInfo): void
    {
        [$sqlState, $errorCode, $errorMessage] = $errorInfo;
        $this->logger->error("{$errorCode} SQLSTATE[{$sqlState}]: '{$errorMessage}'");

        throw new PDOException($errorMessage, $errorCode);
    }

    private function getMapper(string $className): ORMMapper
    {
        return new ORMMapper($className);
    }

    private function execute(string $sql, array $params = []): PDOStatement
    {
        $this->connect();
        $stmt = $this->pdo->prepare($sql);
        if (! $stmt instanceof PDOStatement) {
            $this->throwExceptionFromArray($this->pdo->errorInfo());
        }

        $index = 0;
        foreach ($params as $key => $value) {
            if (! is_string($key) || $key === '') {
                $key = ++$index;
            } elseif ($key[0] !== ':') {
                $key = ":{$key}";
            }

            switch (true) {
                case is_bool($value):
                    $stmt->bindValue($key, $value, PDO::PARAM_BOOL);
                    break;
                case is_int($value):
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                    break;
                case is_null($value):
                    $stmt->bindValue($key, $value, PDO::PARAM_NULL);
                    break;
                case is_resource($value):
                    $stmt->bindValue($key, $value, PDO::PARAM_LOB);
                    break;
                case is_string($value):
                    // fall through
                default:
                    $stmt->bindValue($key, $value, PDO::PARAM_STR);
                    break;
            }
        }

        $this->logger->info($sql);
        if (! $stmt->execute()) {
            $this->throwExceptionFromArray($this->pdo->errorInfo());
        }

        return $stmt;
    }

    public function query(string $sql, array $params = []): QueryResult
    {
        $stmt = $this->execute($sql, $params);
        return new QueryResult(
            $stmt->rowCount(),
            (string)$this->pdo->lastInsertId()
        );
    }

    public function fetchOne(string $className, string $sql, array $params = [])
    {
        $stmt   = $this->execute($sql, $params);
        $mapper = $this->getMapper($className);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! is_array($record)) {
            return null;
        }

        return $mapper->recordToObjectInstance($record);
    }

    public function fetchAll(string $className, string $sql, array $params = [])
    {
        $mapper  = $this->getMapper($className);
        $stmt    = $this->execute($sql, $params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static function (array $record) use ($mapper) {
                return $mapper->recordToObjectInstance($record);
            },
            $records
        );
    }

    public function fetchValue(string $sql, array $params = [])
    {
        $stmt   = $this->execute($sql, $params);
        $column = $stmt->fetchColumn(0);

        if ($column === false) {
            return null;
        }

        return $column;
    }
}
