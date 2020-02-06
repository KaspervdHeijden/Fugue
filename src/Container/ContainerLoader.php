<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Configuration\ConfigurationNotFoundException;
use Fugue\Configuration\Loader\ConfigurationLoaderInterface;
use Fugue\Persistence\Database\DatabaseConnectionSettings;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\Collection\Collection;
use Fugue\Core\Kernel;

final class ContainerLoader
{
    /** @var string */
    private const CONFIG_ID_DATABASE_CONFIG = 'db-config';

    /** @var string */
    private const CONFIG_ID_SERVICES = 'services';

    /** @var string */
    private const CONFIG_ID_ROUTES = 'routes';

    /** @var ConfigurationLoaderInterface[] */
    private $configLoaders;

    public function __construct(ConfigurationLoaderInterface ...$configLoaders)
    {
        $this->configLoaders = $configLoaders;
    }

    private function loadConfiguration(string $identifier): Collection
    {
        foreach ($this->configLoaders as $loader) {
            if ($loader->supports($identifier)) {
                return $loader->load($identifier);
            }
        }

        throw ConfigurationNotFoundException::forIdentifier($identifier);
    }

    private function loadRoutes(): RouteCollectionMap
    {
        return new RouteCollectionMap(
            $this->loadConfiguration(self::CONFIG_ID_ROUTES)
        );
    }

    private function getDatabaseConnectionSettings(): DatabaseConnectionSettings
    {
        $mapping = $this->loadConfiguration(self::CONFIG_ID_DATABASE_CONFIG);
        return new DatabaseConnectionSettings(
            $mapping['dsn'],
            $mapping['user'],
            $mapping['password'],
            $mapping['charset'],
            $mapping['timezone'] ?? '',
            $mapping['options'] ?? null
        );
    }

    public function createForKernel(Kernel $kernel): Container
    {
        return new Container(
            ContainerDefinition::raw(Kernel::class, $kernel),
            ContainerDefinition::raw(
                OutputHandlerInterface::class,
                $kernel->getOutputHandler()
            ),
            ContainerDefinition::singleton(
                RouteCollectionMap::class,
                [$this, 'loadRoutes']
            ),
            ContainerDefinition::singleton(
                DatabaseConnectionSettings::class,
                [$this, 'getDatabaseConnectionSettings']
            ),
            ...$this->loadConfiguration(self::CONFIG_ID_SERVICES)->toArray()
        );
    }
}
