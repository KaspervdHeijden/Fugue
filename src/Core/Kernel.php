<?php

declare(strict_types=1);

namespace Fugue\Core;

use Fugue\Core\ClassLoader\ClassLoaderInterface;
use Fugue\Core\Exception\ErrorHandlerInterface;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\Container\ContainerLoader;
use Fugue\Container\Container;

final class Kernel
{
    /** @var ContainerLoader */
    private $containerLoader;

    /** @var OutputHandlerInterface */
    private $outputHandler;

    /** @var ErrorHandlerInterface */
    private $errorHandler;

    /** @var ClassLoaderInterface */
    private $classLoader;

    /** @var Container */
    private $container;

    /**
     * @param OutputHandlerInterface $outputHandler   Where to write output to.
     * @param ErrorHandlerInterface  $errorHandler    The error handler to use.
     * @param ClassLoaderInterface   $classLoader     The classloader to use.
     * @param ContainerLoader        $containerLoader Object to load a container.
     */
    public function __construct(
        OutputhandlerInterface $outputHandler,
        ErrorHandlerInterface $errorHandler,
        ClassLoaderInterface $classLoader,
        ContainerLoader $containerLoader
    ) {
        $this->containerLoader = $containerLoader;
        $this->outputHandler   = $outputHandler;
        $this->errorHandler    = $errorHandler;
        $this->classLoader     = $classLoader;

        $errorHandler->register();
        $classLoader->register();
    }

    public function getContainer(): Container
    {
        if (! $this->container instanceof Container) {
            $this->container = $this->containerLoader->createForKernel($this);
        }

        return $this->container;
    }

    public function getOutputHandler(): OutputHandlerInterface
    {
        return $this->outputHandler;
    }
}
