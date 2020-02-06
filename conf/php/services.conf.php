<?php

declare(strict_types=1);

use Fugue\Localization\Formatting\PhoneNumber\PhoneNumberFormatterInterface;
use Fugue\Localization\Formatting\PhoneNumber\EnglishPhoneNumberFormatter;
use Fugue\Localization\Formatting\Number\NumberFormatterInterface;
use Fugue\Localization\Formatting\Date\DateFormatterInterface;
use Fugue\Persistence\Database\DatabaseQueryAdapterInterface;
use Fugue\Localization\Formatting\Date\EnglishDateFormatter;
use Fugue\Persistence\Database\PdoMySqlDatabaseQueryAdapter;
use Fugue\Persistence\Database\DatabaseConnectionSettings;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\Core\Output\NullOutputHandler;
use Fugue\Container\ContainerDefinition;
use Fugue\View\Templating\TemplateUtil;
use Fugue\Container\Container;

return [
    ContainerDefinition::raw(
        PhoneNumberFormatterInterface::class,
        new EnglishPhoneNumberFormatter()
    ),

    ContainerDefinition::raw(
        NumberFormatterInterface::class,
        new EnglishDateFormatter()
    ),

    ContainerDefinition::raw(
        DateFormatterInterface::class,
        new EnglishDateFormatter()
    ),

    ContainerDefinition::raw(
        OutputHandlerInterface::class,
        new NullOutputHandler()
    ),

    ContainerDefinition::singleton(
        TemplateUtil::class,
        static function (Container $container): TemplateUtil {
            return new TemplateUtil(
                $container->resolve(PhoneNumberFormatterInterface::class),
                $container->resolve(NumberFormatterInterface::class),
                $container->resolve(DateFormatterInterface::class),
                $container->resolve(OutputHandlerInterface::class),
                $container->resolve(RouteCollectionMap::class)
            );
        }
    ),

    ContainerDefinition::singleton(
        DatabaseQueryAdapterInterface::class,
        static function (Container $container): DatabaseQueryAdapterInterface {
            return new PdoMySqlDatabaseQueryAdapter(
                $container->resolve(DatabaseConnectionSettings::class),
                $container->resolve(OutputHandlerInterface::class)
            );
        }
    ),
];
