<?php

declare(strict_types=1);

namespace Fugue\Persistence\Database;

use Fugue\Configuration\SettingBranch;
use Fugue\Logging\LoggerInterface;
use InvalidArgumentException;

final class DatabaseAdapterFactory
{
    /**
     * @var string The PDO database implementation identifier.
     */
    public const DATABASE_ADAPTER_PDO_MYSQL = 'pdo-mysql';

    /**
     * @var string The empty database implementation identifier.
     */
    public const DATABASE_ADAPTER_EMPTY     = 'empty';

    /**
     * @var string The system default database adapter identifier.
     */
    public const DATABASE_ADAPTER_DEFAULT   = 'default';

    /** @var SettingBranch */
    private $settingBranch;

    public function __construct(SettingBranch $settingBranch)
    {
        $this->settingBranch = $settingBranch;
    }

    /**
     * Gets a DatabaseAdapterInterface from an identifier.
     *
     * @param string          $identifier    The identifier to get the DatabaseAdapterInterface for.
     * @param LoggerInterface $logger        The LoggerInterface to use.
     *
     * @return DatabaseQueryAdapterInterface The DatabaseAdapterInterface.
     */
    public function getDatabaseAdapterFromIdentifier(
        string $identifier,
        LoggerInterface $logger
    ): DatabaseQueryAdapterInterface {
        switch ($identifier) {
            case self::DATABASE_ADAPTER_PDO_MYSQL:
                return new PdoMySqlDatabaseQueryAdapter($this->settingBranch, $logger);
            case self::DATABASE_ADAPTER_DEFAULT:
                return $this->getDatabaseAdapterFromIdentifier(
                    (string)($this->settingBranch['identifier'] ?? 'blackhole'),
                    $logger
                );
            case self::DATABASE_ADAPTER_EMPTY:
                return new EmptyDatabaseQueryAdapter();
            default:
                throw new InvalidArgumentException(
                    "Identifier {$identifier} not recognized."
                );
        }
    }
}

