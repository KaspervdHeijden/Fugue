<?php

declare(strict_types=1);

use Fugue\Persistence\Database\DatabaseQueryAdapterInterface;
use Fugue\Persistence\Database\PdoMySqlDatabaseQueryAdapter;
use Fugue\Persistence\Database\DatabaseConnectionSettings;
use Fugue\Container\SingletonContainerDefinition;
use Fugue\Container\ContainerDefinition;
use Fugue\Container\Container;
use Fugue\Logging\EmptyLogger;
use Fugue\Core\Kernel;

/** @return ContainerDefinition[] */
return [
    new SingletonContainerDefinition(
        DatabaseQueryAdapterInterface::class,
        static function (Container $container): DatabaseQueryAdapterInterface {
            /** @var Kernel $kernel */
            $kernel   = $container->resolve(Kernel::class);
            $settings = $kernel->getConfigLoader()->load('database.conf');
            $logger   = $kernel->resolveClass($settings['logger'], $container);
            $config   = new DatabaseConnectionSettings(
                $settings['dsn'],
                $settings['user'],
                $settings['password'],
                $settings['charset'] ?? 'UTF-8',
                $settings['timezone'] ?? 'UTC',
                $settings['options'] ?? []
            );

            return new PdoMySqlDatabaseQueryAdapter(
                $config,
                $logger ?: new EmptyLogger()
            );
        }
    ),
];
