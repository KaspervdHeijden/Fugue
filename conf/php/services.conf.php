<?php

declare(strict_types=1);

use Fugue\Persistence\Database\DatabaseQueryAdapterInterface;
use Fugue\Configuration\Loader\ConfigurationLoaderInterface;
use Fugue\Persistence\Database\PdoMySqlDatabaseQueryAdapter;
use Fugue\Persistence\Database\DatabaseConnectionSettings;
use Fugue\Container\SingletonContainerDefinition;
use Fugue\Container\ContainerDefinition;
use Fugue\Container\Container;
use Fugue\Logging\EmptyLogger;

/** @return ContainerDefinition[] */
return [
    new SingletonContainerDefinition(
        DatabaseQueryAdapterInterface::class,
        static function (Container $container): DatabaseQueryAdapterInterface {
            $loader   = $container->resolve(ConfigurationLoaderInterface::class);
            $settings = $loader->load('database.conf');
            $config   = new DatabaseConnectionSettings(
                $settings['dsn'],
                $settings['user'],
                $settings['password'],
                $settings['charset'],
                $settings['timezone'],
                $settings['options']
            );

            return new PdoMySqlDatabaseQueryAdapter($config, new EmptyLogger());
        }
    ),
];
