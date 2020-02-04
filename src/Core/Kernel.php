<?php

declare(strict_types=1);

namespace Fugue\Core;

use Fugue\Configuration\Loader\ConfigurationLoaderInterface;
use Fugue\Configuration\ConfigurationNotFoundException;
use Fugue\Core\ClassLoader\ClassLoaderInterface;
use Fugue\Core\Exception\ErrorHandlerInterface;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\Collection\CollectionMap;
use Fugue\Container\Container;

final class Kernel
{
    private const CONFIG_ID_SERVICES = 'services';

    /** @var string */
    private const NAMESPACE_BASE = 'Fugue';

    /** @var ConfigurationLoaderInterface[] */
    private $configLoaders;

    /** @var OutputHandlerInterface */
    private $outputHandler;

    /** @var ErrorHandlerInterface */
    private $errorHandler;

    /** @var ClassLoaderInterface */
    private $classLoader;

    /** @var Container */
    private $container;

    /**
     * Instantiates the framework.
     *
     * Although this is not static, Fugue does NOT support multiple instances of the Kernel.
     *
     * @param OutputHandlerInterface         $outputHandler Where to write output to.
     * @param ErrorHandlerInterface          $errorHandler  The error handler to use.
     * @param ClassLoaderInterface           $classLoader   The classloader to use.
     * @param ConfigurationLoaderInterface[] $configLoaders Used to load configurations.
     */
    public function __construct(
        OutputhandlerInterface $outputHandler,
        ErrorHandlerInterface $errorHandler,
        ClassLoaderInterface $classLoader,
        array $configLoaders
    ) {
        $this->configLoaders = $configLoaders;
        $this->outputHandler = $outputHandler;
        $this->errorHandler  = $errorHandler;
        $this->classLoader   = $classLoader;

        $errorHandler->register();
        $classLoader->register();
    }

    /**
     * Loads a configuration file.
     *
     * @param string $identifier Identifies the configuration item to load.
     * @return CollectionMap     Results returned from the configuration loader.
     */
    public function loadConfiguration(string $identifier): CollectionMap
    {
        foreach ($this->configLoaders as $loader) {
            if ($loader->supports($identifier)) {
                return $loader->load($identifier);
            }
        }

        throw ConfigurationNotFoundException::forIdentifier($identifier);
    }

    public function getContainer(): Container
    {
        if (! $this->container instanceof Container) {
            $services        = $this->loadConfiguration(self::CONFIG_ID_SERVICES);
            $this->container = new Container(...$services->all());
        }

        return $this->container;
    }

    public function getOutputHandler(): OutputHandlerInterface
    {
        return $this->outputHandler;
    }
}
