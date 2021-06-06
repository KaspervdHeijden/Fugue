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
use Fugue\IO\Filesystem\FileSystemInterface;
use Fugue\Core\Output\StreamOutputHandler;
use Fugue\IO\Filesystem\NativeFileSystem;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\IO\Stream\StreamStreamWriter;
use Fugue\Container\ContainerLoader;
use Fugue\Logging\LoggerInterface;
use Fugue\Container\ClassResolver;
use Fugue\Logging\OutputLogger;
use Fugue\Caching\MemoryCache;
use Fugue\Container\Container;
use Fugue\HTTP\Request;
use Throwable;

use function spl_autoload_register;
use function mb_internal_encoding;
use function mb_regex_encoding;
use function set_error_handler;
use function mb_http_output;
use function mb_substr;
use function ini_set;
use function rtrim;

use const E_ALL;

abstract class Kernel
{
    private ExceptionHandlerInterface $exceptionHandler;
    private ConfigurationLoaderInterface $configLoader;
    private OutputhandlerInterface $outputHandler;
    private ClassLoaderInterface $classLoader;
    private ClassResolver $classResolver;
    private LoggerInterface $logger;

    /**
     * This method mutates global runtime state,
     * and should therefore be called only once.
     */
    public function __construct(
        ?OutputHandlerInterface $outputHandler       = null,
        ?ConfigurationLoaderInterface $configLoader  = null,
        ?ExceptionHandlerInterface $exceptionHandler = null,
        ?LoggerInterface $logger                     = null,
        ?ClassLoaderInterface $classLoader           = null,
        FileSystemInterface $fileSystem              = null
    ) {
        $srcDir            = $this->getSrcDir();
        $fileSystem        = $fileSystem ?: new NativeFileSystem();
        $this->classLoader = $classLoader ?: new DefaultClassLoader($fileSystem, $srcDir);

        spl_autoload_register([$this->classLoader, 'loadClass'], true, true);

        $this->classResolver    = new ClassResolver(new MemoryCache());
        $this->outputHandler    = $outputHandler ?: new StreamOutputHandler(StreamStreamWriter::forStdOut());
        $this->exceptionHandler = $exceptionHandler ?: new OutputExceptionHandler($this->outputHandler);

        set_error_handler([$this, 'handleUnexpectedException']);

        $confDirectory      = "{$srcDir}/../conf";
        $charset            = $this->getCharset();
        $this->logger       = $logger ?: new OutputLogger($this->outputHandler);
        $this->configLoader = $configLoader ?: new MultiConfigurationLoader(
            new JsonConfigurationLoader($fileSystem, $confDirectory, 'json'),
            new IniConfigurationLoader($fileSystem, $confDirectory, 'ini'),
            new PHPConfigurationLoader($fileSystem, $confDirectory, 'php'),
        );

        ini_set('display_errors', $this->displayErrors() ? '1' : '0');
        ini_set('error_reporting', (string)$this->getErrorLevel());
        ini_set('default_charset', $charset);

        mb_internal_encoding($charset);
        mb_regex_encoding($charset);
        mb_http_output($charset);
    }

    public function getExceptionHandler(): ExceptionHandlerInterface
    {
        return $this->exceptionHandler;
    }

    public function getConfigLoader(): ConfigurationLoaderInterface
    {
        return $this->configLoader;
    }

    public function getOutputHandler(): OutputHandlerInterface
    {
        return $this->outputHandler;
    }

    public function getClassLoader(): ClassLoaderInterface
    {
        return $this->classLoader;
    }

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getSrcDir(): string
    {
        return mb_substr(rtrim(__DIR__, DIRECTORY_SEPARATOR), 0, -10);
    }

    protected function getCharset(): string
    {
        return 'utf-8';
    }

    protected function displayErrors(): bool
    {
        return true;
    }

    protected function getErrorLevel(): int
    {
        return E_ALL;
    }

    final public function handleUnexpectedException(
        int $code,
        string $message,
        string $file,
        int $line
    ): bool {
        if (($code & $this->getErrorLevel()) === 0) {
            return true;
        }

        try {
            $this->exceptionHandler->handle(
                UnhandledErrorException::create(
                    $code,
                    $message,
                    $file,
                    $line,
                )
            );
        } catch (Throwable $throwable) {
            $this->outputHandler->write($throwable->getTraceAsString());
        }

        exit($code < 1 ? 1 : $code);
    }

    final public function resolveClass(string $className, Container $container): mixed
    {
        return $this->classResolver->resolve($className, $container);
    }

    abstract protected function createRuntime(Container $container): RuntimeInterface;

    public function handleRequest(Request $request): void
    {
        $loader    = new ContainerLoader($this->configLoader);
        $container = $loader->createForKernel($this);

        $runtime = $this->createRuntime($container);
        $runtime->handle($request);
    }
}
