<?php

declare(strict_types=1);

use Fugue\Persistence\Database\DatabaseQueryAdapterInterface;
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
            return new PdoMySqlDatabaseQueryAdapter(
                $container->resolve(DatabaseConnectionSettings::class),
                new EmptyLogger()
            );
        }
    ),
];
