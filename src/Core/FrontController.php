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
use Fugue\Collection\PropertyBag;
use Fugue\Logging\OutputLogger;
use Fugue\Caching\MemoryCache;
use Fugue\HTTP\Request;
use Throwable;

use function spl_autoload_register;
use function mb_internal_encoding;
use function set_error_handler;
use function mb_regex_encoding;
use function mb_http_output;
use function mb_http_input;
use function ini_set;
use function rtrim;

abstract class FrontController
{
    /** @var string */
    private const CONF_DIR_PATH = '/../conf';

    /** @var string */
    public const ROOT_NAMESPACE = 'Fugue';

    /** @var ExceptionHandlerInterface */
    private $exceptionHandler;

    /** @var ClassLoaderInterface */
    private $classLoader;

    /** @var OutputHandlerInterface */
    private $outputHandler;

    /** @var LoggerInterface */
    private $logger;

    /** @var bool */
    private $displayErrors;

    /** @var int */
    private $errorLevel;

    /** @var string */
    private $charset;

    /** @var Kernel */
    private $kernel;

    public function __construct(
        int $errorLevel,
        string $charset,
        bool $displayErrors
    ) {
        $this->displayErrors = $displayErrors;
        $this->errorLevel    = $errorLevel;
        $this->charset       = $charset;
    }

    final protected function handleUnexpectedException(
        int $code,
        string $message,
        string $file,
        int $line
    ): void {
        try {
            if (($this->getErrorLevel() & $code) === 0) {
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

    protected function getClassLoader(): ClassLoaderInterface
    {
        if (! $this->classLoader instanceof ClassLoaderInterface) {
            $this->classLoader = new DefaultClassLoader(
                rtrim(__DIR__, '/'),
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
            $this->logger = new OutputLogger($this->getOutputHandler());
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
                new ContainerLoader($this->getConfigurationLoaders(__DIR__ . self::CONF_DIR_PATH)),
                $this->getLogger()
            );
        }

        return $this->kernel;
    }

    final protected function getErrorLevel(): int
    {
        return $this->errorLevel;
    }

    abstract protected function createRuntime(): RuntimeInterface;

    /**
     * This method mutates global runtime state,
     * and should therefore be called only once.
     */
    protected function initializeGlobalState(): void
    {
        ini_set('display_errors', ($this->displayErrors) ? '1' : '0');
        ini_set('error_reporting', (string)$this->getErrorLevel());
        ini_set('default_charset', $this->charset);

        mb_internal_encoding($this->charset);
        mb_regex_encoding($this->charset);
        mb_http_output($this->charset);
        mb_http_input($this->charset);

        spl_autoload_register([$this->getClassLoader(), 'loadClass'], true, true);
        set_error_handler([$this, 'handleUnexpectedException']);
    }

    public function handleRequest(
        array $get,
        array $post,
        array $cookie,
        array $files,
        array $server
    ): void {
        $this->initializeGlobalState();
        $this->createRuntime()->handle(
            new Request(
                new PropertyBag($get),
                new PropertyBag($post),
                new PropertyBag($cookie),
                new PropertyBag($files),
                new PropertyBag($server)
            )
        );
    }
}
