<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Configuration\Loader\ConfigurationLoaderInterface;
use Fugue\Core\Exception\ExceptionHandlerInterface;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\Logging\LoggerInterface;
use Fugue\Core\Kernel;

final class ContainerLoader
{
    private const CONFIG_ID_SERVICES = 'services';
    private const CONFIG_ID_ROUTES   = 'routes';

    private ConfigurationLoaderInterface $configLoader;

    public function __construct(ConfigurationLoaderInterface $configLoader)
    {
        $this->configLoader = $configLoader;
    }

    public function createForKernel(Kernel $kernel): Container
    {
        $container = new Container(
            new RawContainerDefinition(Kernel::class, $kernel),
            new RawContainerDefinition(
                OutputHandlerInterface::class,
                $kernel->getOutputHandler()
            ),
            new RawContainerDefinition(
                LoggerInterface::class,
                $kernel->getLogger()
            ),
            new RawContainerDefinition(
                ExceptionHandlerInterface::class,
                $kernel->getExceptionHandler()
            ),
            new RawContainerDefinition(
                ConfigurationLoaderInterface::class,
                $this->configLoader
            )
        );

        if ($this->configLoader->supports(self::CONFIG_ID_ROUTES)) {
            $container->register(
                new SingletonContainerDefinition(
                    RouteCollectionMap::class,
                    function (): RouteCollectionMap {
                        return new RouteCollectionMap(
                            $this->configLoader->load(self::CONFIG_ID_ROUTES)
                        );
                    }
                ),
            );
        }

        if ($this->configLoader->supports(self::CONFIG_ID_SERVICES)) {
            $definitions = $this->configLoader->load(self::CONFIG_ID_SERVICES);
            foreach ($definitions as $definition) {
                $container->register($definition);
            }
        }

        return $container;
    }
}
