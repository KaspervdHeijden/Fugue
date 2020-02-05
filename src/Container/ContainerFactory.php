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

final class ContainerFactory
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

    public function createForKernel(Kernel $kernel): Container
    {
        $container  = new Container(ContainerDefinition::raw(Kernel::class, $kernel));
        $services   = $this->loadConfiguration(self::CONFIG_ID_SERVICES);
        $services[] = ContainerDefinition::raw(
            OutputHandlerInterface::class,
            $kernel->getOutputHandler()
        );

        $services[] = ContainerDefinition::singleton(
            RouteCollectionMap::class,
            function (): RouteCollectionMap {
                return new RouteCollectionMap($this->loadConfiguration(self::CONFIG_ID_ROUTES));
            }
        );

        $services[] = ContainerDefinition::singleton(
            DatabaseConnectionSettings::class,
            function (): DatabaseConnectionSettings {
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
        );

        foreach ($services->all() as $service) {
            $container->register($service);
        }

        return $container;
    }
}
