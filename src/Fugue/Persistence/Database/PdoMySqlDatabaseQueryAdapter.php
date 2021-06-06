<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

use Fugue\Persistence\Database\ORM\RecordMapperInterface;
use Fugue\Persistence\Database\ORM\ReflectionClassMapper;
use Fugue\Persistence\Database\ORM\StdClassMapper;
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
    private DatabaseConnectionSettings $settings;
    private LoggerInterface $logger;
    private ?PDO $pdo = null;

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
            $this->settings->getHost(),
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
                throw new RuntimeException('Could not set time zone');
            }
        }
    }

    private function throwExceptionFromArray(array $errorInfo): void
    {
        [$sqlState, $errorCode, $errorMessage] = $errorInfo;
        $this->logger->error("{$errorCode} SQLSTATE[{$sqlState}]: '{$errorMessage}'");

        throw new PDOException($errorMessage, $errorCode);
    }

    private function getMapper(?string $className): RecordMapperInterface
    {
        if ($className === null || $className === '') {
            return new StdClassMapper();
        }

        return new ReflectionClassMapper($className);
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
                    $stmt->bindValue($key, $value);
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
        $stmt       = $this->execute($sql, $params);
        $insertedId = (string)$this->pdo->lastInsertId();

        return new QueryResult(
            (int)$stmt->rowCount(),
            ($insertedId === '') ? null : $insertedId
        );
    }

    public function fetchOne(
        string $sql,
        array $params = [],
        ?string $className = null
    ): ?object {
        $mapper = $this->getMapper($className);
        $stmt   = $this->execute($sql, $params);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (! is_array($record)) {
            return null;
        }

        return $mapper->arrayToObject($record);
    }

    public function fetchAll(
        string $sql,
        array $params = [],
        ?string $className = null
    ): array {
        $mapper  = $this->getMapper($className);
        $stmt    = $this->execute($sql, $params);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            static fn (array $record): object => $mapper->arrayToObject($record),
            $records
        );
    }

    public function fetchValue(string $sql, array $params = []): float|int|bool|string|null
    {
        $stmt   = $this->execute($sql, $params);
        $column = $stmt->fetchColumn();

        if ($column === false) {
            return null;
        }

        return $column;
    }
}
