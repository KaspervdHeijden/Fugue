<?php

declare(strict_types=1);

namespace Fugue\Core;

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
use Fugue\Container\ClassResolver;
use Fugue\Logging\LoggerInterface;
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
use function realpath;
use function ini_set;
use function rtrim;

abstract class FrontController
{
    /** @var string */
    private const CONF_DIR_PATH = '/../conf';

    /** @var string */
    public const ROOT_NAMESPACE = 'Fugue';

    private ?ExceptionHandlerInterface $exceptionHandler = null;

    private ?OutputHandlerInterface $outputHandler = null;

    private ?ClassLoaderInterface $classLoader = null;

    private ?LoggerInterface $logger = null;

    private ?Kernel $kernel = null;

    private int $errorLevel;

    public function __construct(
        int $errorLevel,
        string $charset,
        bool $displayErrors
    ) {
        $this->initializeGlobalState($charset, $errorLevel, $displayErrors);
        $this->errorLevel = $errorLevel;
    }

    final public function handleUnexpectedException(
        int $code,
        string $message,
        string $file,
        int $line
    ): void {
        try {
            if (($this->errorLevel & $code) === 0) {
                return;
            }

            $this->getExceptionHandler()->handle(
                UnhandledErrorException::create(
                    $code,
                    $message,
                    $file,
                    $line
                )
            );
        } catch (Throwable $throwable) {
            $this->getOutputHandler()
                 ->write($throwable->getTraceAsString());
        }

        exit($code < 1 ? 1 : $code);
    }

    protected function getClassResolver(): ClassResolver
    {
        return new ClassResolver(new MemoryCache());
    }

    private function getRootDir(string $path): string
    {
        return realpath(rtrim(__DIR__, '/') . "/..{$path}");
    }

    protected function getClassLoader(): ClassLoaderInterface
    {
        if (! $this->classLoader instanceof ClassLoaderInterface) {
            $this->classLoader = new DefaultClassLoader(
                $this->getRootDir(''),
                self::ROOT_NAMESPACE
            );
        }

        return $this->classLoader;
    }

    protected function getOutputHandler(): OutputHandlerInterface
    {
        if (! $this->outputHandler instanceof OutputHandlerInterface) {
            $this->outputHandler = new StandardOutputHandler();
        }

        return $this->outputHandler;
    }

    protected function getExceptionHandler(): ExceptionHandlerInterface
    {
        if (! $this->exceptionHandler instanceof ExceptionHandlerInterface) {
            $this->exceptionHandler = new OutputExceptionHandler($this->getOutputHandler());
        }

        return $this->exceptionHandler;
    }

    protected function getConfigurationLoaders(string $configDir): array
    {
        return [
            new IniConfigurationLoader($configDir),
            new PHPConfigurationLoader($configDir),
        ];
    }

    protected function getLogger(): LoggerInterface
    {
        if (! $this->logger instanceof LoggerInterface) {
            $this->logger = new OutputLogger($this->getOutputHandler(), true);
        }

        return $this->logger;
    }

    protected function getKernel(): Kernel
    {
        if (! $this->kernel instanceof Kernel) {
            $this->kernel = new Kernel(
                $this->getExceptionHandler(),
                $this->getOutputHandler(),
                $this->getClassLoader(),
                new ContainerLoader(...$this->getConfigurationLoaders($this->getRootDir(self::CONF_DIR_PATH))),
                $this->getLogger()
            );
        }

        return $this->kernel;
    }

    abstract protected function createRuntime(): RuntimeInterface;

    /**
     * This method mutates global runtime state,
     * and should therefore be called only once.
     */
    private function initializeGlobalState(
        string $charset,
        int $errorLevel,
        bool $displayErrors
    ): void {
        ini_set('display_errors', ($displayErrors) ? '1' : '0');
        ini_set('error_reporting', (string)$errorLevel);
        ini_set('default_charset', $charset);

        mb_internal_encoding($charset);
        mb_regex_encoding($charset);
        mb_http_output($charset);
        mb_http_input($charset);

        spl_autoload_register([$this->getClassLoader(), 'loadClass'], true, true);
        set_error_handler([$this, 'handleUnexpectedException']);
    }

    public function handleRequest(Request $request): void
    {
        $this->createRuntime()->handle($request);
    }
}
