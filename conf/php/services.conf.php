<?php

declare(strict_types=1);

use Fugue\Localization\Formatting\Number\NumberFormatterInterface;
use Fugue\Localization\Formatting\Number\DefaultNumberFormatter;
use Fugue\Localization\Formatting\Date\DateFormatterInterface;
use Fugue\Persistence\Database\DatabaseQueryAdapterInterface;
use Fugue\Localization\Formatting\Date\DefaultDateFormatter;
use Fugue\Persistence\Database\PdoMySqlDatabaseQueryAdapter;
use Fugue\Persistence\Database\DatabaseConnectionSettings;
use Fugue\Container\ContainerDefinition;
use Fugue\Container\Container;
use Fugue\Logging\EmptyLogger;

/** @return ContainerDefinition[] */
return [
    ContainerDefinition::raw(
        DateFormatterInterface::class,
        DefaultDateFormatter::forMysql()
    ),

    ContainerDefinition::raw(
        NumberFormatterInterface::class,
        new DefaultNumberFormatter()
    ),

    ContainerDefinition::singleton(
        DatabaseQueryAdapterInterface::class,
        static function (Container $container): DatabaseQueryAdapterInterface {
            return new PdoMySqlDatabaseQueryAdapter(
                $container->resolve(DatabaseConnectionSettings::class),
                new EmptyLogger()
            );
        }
    ),
];
