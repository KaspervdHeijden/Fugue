<?php

declare(strict_types=1);

namespace Fugue\Core;

use Fugue\Configuration\Loader\ConfigurationLoaderInterface;
use Fugue\Configuration\Loader\MultiConfigurationLoader;
use Fugue\Configuration\Loader\JsonConfigurationLoader;
use Fugue\Configuration\Loader\IniConfigurationLoader;
use Fugue\Configuration\Loader\PHPConfigurationLoader;
use Fugue\Core\Exception\ExceptionHandlerInterface;
use Fugue\Core\Exception\UnhandledErrorException;
use Fugue\Core\Exception\OutputExceptionHandler;
use Fugue\Core\ClassLoader\ClassLoaderInterface;
use Fugue\Core\ClassLoader\DefaultClassLoader;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\Core\Output\StandardOutputHandler;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Container\ContainerLoader;
use Fugue\Logging\LoggerInterface;
use Fugue\Container\ClassResolver;
use Fugue\Logging\OutputLogger;
use Fugue\Caching\MemoryCache;
use Fugue\HTTP\Request;
use Throwable;

use function spl_autoload_register;
use function mb_internal_encoding;
use function mb_regex_encoding;
use function set_error_handler;
use function mb_http_output;
use function mb_http_input;
use function array_slice;
use function mb_substr;
use function explode;
use function ini_set;
use function rtrim;

abstract class FrontController
{
    private int $errorLevel;
    private Kernel $kernel;

    /**
     * This method mutates global runtime state,
     * and should therefore be called only once.
     */
    public function __construct(
        int $errorLevel,
        string $charset,
        bool $displayErrors,
        ?ClassLoaderInterface $classLoader           = null,
        ?OutputHandlerInterface $outputHandler       = null,
        ?ConfigurationLoaderInterface $configLoader  = null,
        ?ExceptionHandlerInterface $exceptionHandler = null,
        ?LoggerInterface $logger                     = null
    ) {
        $rootDir = mb_substr(rtrim(__DIR__, DIRECTORY_SEPARATOR), 0, -4);
        if (! $classLoader instanceof ClassLoaderInterface) {
            $root        = array_slice(explode('\\', self::class), 0, 1)[0];
            $classLoader = new DefaultClassLoader($rootDir, $root);
        }

        spl_autoload_register([$classLoader, 'loadClass'], true, true);

        $outputHandler = $outputHandler ?: new StandardOutputHandler();
        $configLoader  = $configLoader ?: new MultiConfigurationLoader(
            new JsonConfigurationLoader("{$rootDir}/../conf", 'json'),
            new IniConfigurationLoader("{$rootDir}/../conf", 'ini'),
            new PHPConfigurationLoader("{$rootDir}/../conf", 'php'),
        );

        $this->errorLevel = $errorLevel;
        $this->kernel     = $this->createKernel(
            $exceptionHandler ?: new OutputExceptionHandler($outputHandler),
            $outputHandler,
            $classLoader,
            $configLoader,
            $logger ?: new OutputLogger($outputHandler, true),
        );

        set_error_handler([$this, 'handleUnexpectedException']);

        ini_set('display_errors', ($displayErrors) ? '1' : '0');
        ini_set('error_reporting', (string)$errorLevel);
        ini_set('default_charset', $charset);

        mb_internal_encoding($charset);
        mb_regex_encoding($charset);
        mb_http_output($charset);
        mb_http_input($charset);
    }

    protected function createKernel(
        ExceptionHandlerInterface $exceptionHandler,
        OutputHandlerInterface $outputHandler,
        ClassLoaderInterface $classLoader,
        ConfigurationLoaderInterface $configLoader,
        LoggerInterface $logger
    ): Kernel {
        return new Kernel(
            $exceptionHandler,
            $outputHandler,
            $classLoader,
            new ContainerLoader($configLoader),
            $logger,
        );
    }

    final public function handleUnexpectedException(
        int $code,
        string $message,
        string $file,
        int $line
    ): void {
        if (($code & $this->errorLevel) === 0) {
            return;
        }

        try {
            $this->kernel->getExceptionHandler()->handle(
                UnhandledErrorException::create(
                    $code,
                    $message,
                    $file,
                    $line,
                )
            );
        } catch (Throwable $throwable) {
            $this->kernel
                 ->getOutputHandler()
                 ->write($throwable->getTraceAsString());
        }

        exit($code < 1 ? 1 : $code);
    }

    abstract protected function createRuntime(
        Kernel $kernel,
        ClassResolver $classResolver
    ): RuntimeInterface;

    public function handleRequest(Request $request): void
    {
        $resolver = new ClassResolver(new MemoryCache());
        $runtime  = $this->createRuntime($this->kernel, $resolver);

        $runtime->handle($request);
    }
}
