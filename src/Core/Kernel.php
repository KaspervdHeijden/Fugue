<?php

declare(strict_types=1);

namespace Fugue\Core;

use Fugue\Core\Exception\ExceptionHandlerInterface;
use Fugue\Core\ClassLoader\ClassLoaderInterface;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\Container\ContainerLoader;
use Fugue\Logging\LoggerInterface;
use Fugue\Container\Container;

final class Kernel
{
    private ExceptionHandlerInterface $exceptionHandler;
    private OutputHandlerInterface $outputHandler;
    private ClassLoaderInterface $classLoader;
    private ContainerLoader $containerLoader;
    private ?Container $container = null;
    private LoggerInterface $logger;

    public function __construct(
        ExceptionHandlerInterface $exceptionHandler,
        OutputhandlerInterface $outputHandler,
        ClassLoaderInterface $classLoader,
        ContainerLoader $containerLoader,
        LoggerInterface $logger
    ) {
        $this->exceptionHandler = $exceptionHandler;
        $this->containerLoader  = $containerLoader;
        $this->outputHandler    = $outputHandler;
        $this->classLoader      = $classLoader;
        $this->logger           = $logger;
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

    public function getExceptionHandler(): ExceptionHandlerInterface
    {
        return $this->exceptionHandler;
    }

    public function getClassLoader(): ClassLoaderInterface
    {
        return $this->classLoader;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }
}
