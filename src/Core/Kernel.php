<?php

declare(strict_types=1);

namespace Fugue\Core;

use Fugue\Configuration\Loader\ConfigurationLoaderInterface;
use Fugue\Core\ClassLoader\ClassLoaderInterface;
use Fugue\Core\Exception\ErrorHandlerInterface;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\Container\ContainerFactory;
use Fugue\Container\Container;

final class Kernel
{
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
     * @param OutputHandlerInterface         $outputHandler Where to write output to.
     * @param ErrorHandlerInterface          $errorHandler The error handler to use.
     * @param ClassLoaderInterface           $classLoader The classloader to use.
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

    public function getContainer(): Container
    {
        if (! $this->container instanceof Container) {
            $this->container = (new ContainerFactory(...$this->configLoaders))
                                        ->createForKernel($this);
        }

        return $this->container;
    }

    public function getOutputHandler(): OutputHandlerInterface
    {
        return $this->outputHandler;
    }
}
