<?php

declare(strict_types=1);

namespace Fugue\Core;

use Fugue\Core\Exception\ExceptionHandlerInterface;
use Fugue\Core\ClassLoader\ClassLoaderInterface;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\Logging\LoggerInterface;

final class Kernel
{
    private ExceptionHandlerInterface $exceptionHandler;
    private OutputHandlerInterface $outputHandler;
    private ClassLoaderInterface $classLoader;
    private LoggerInterface $logger;

    public function __construct(
        ExceptionHandlerInterface $exceptionHandler,
        OutputhandlerInterface $outputHandler,
        ClassLoaderInterface $classLoader,
        LoggerInterface $logger
    ) {
        $this->exceptionHandler = $exceptionHandler;
        $this->outputHandler    = $outputHandler;
        $this->classLoader      = $classLoader;
        $this->logger           = $logger;
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
