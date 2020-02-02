<?php

declare(strict_types=1);

namespace Fugue\Core;

use Composer\Autoload\ClassLoader;
use Fugue\Core\ClassLoader\ClassLoaderInterface;
use Fugue\Configuration\Loader\ConfigurationLoaderInterface;
use Fugue\Configuration\Loader\PHPConfigurationLoader;
use Fugue\Core\Output\OutputHandlerInterface;
use Fugue\Collection\CollectionMap;
use Fugue\Container\Container;

use function spl_autoload_register;
use function set_error_handler;
use function error_reporting;
use function str_replace;
use function is_readable;
use function is_iterable;
use function sprintf;
use function is_file;

final class Kernel
{
    /** @var string */
    private const NAMESPACE_BASE = 'Fugue';

    /** @var OutputHandlerInterface */
    private $outputHandler;

    /** @var ClassLoaderInterface */
    private $classLoader;

    /** @var Container */
    private $container;

    /**
     * Instantiates the framework.
     *
     * Although this is not static, Fugue does NOT support multiple instances of the Kernel.
     *
     * @param OutputHandlerInterface $outputHandler  Where to write output to.
     * @param bool                   $attachHandlers Whether or not to attach class loader and error handler.
     */
    public function __construct(
        OutputhandlerInterface $outputHandler,
        bool $attachHandlers
    ) {
        $this->outputHandler = $outputHandler;

        if ($attachHandlers) {
            spl_autoload_register([$this, 'genericClassloader'], true, true);
            set_error_handler([$this, 'genericErrorHandler']);
        }
    }

    /**
     * Gets the root directory path, which would be the path to src/.
     *
     * @return string The root path.
     */
    private function getRootDir(): string
    {
        return __DIR__ . '/../';
    }

    /**
     * @return ConfigurationLoaderInterface[]
     */
    private function getConfigurationLoaders(): array
    {
        $configPath = "{$this->getRootDir()}../conf/";

        return [
            new PHPConfigurationLoader($configPath),
        ];
    }

    /**
     * Loads a configuration file.
     *
     * @param string $identifier Identifies the configuration item to load.
     * @return CollectionMap          Result returned from the included file/
     */
    public function loadConfiguration(string $identifier): CollectionMap
    {
        $loaders = $this->getConfigurationLoaders();
        foreach ($loaders as $loader) {
            if (! $loader->supports($identifier)) {
                continue;
            }

            $result = $loader->load($identifier);
            if (is_iterable($result)) {
                return new CollectionMap($result);
            }
        }

        return new CollectionMap();
    }

    public function getContainer(): Container
    {
        if (! $this->container instanceof Container) {
            $services        = $this->loadConfiguration('services');
            $this->container = new Container(...$services->all());
        }

        return $this->container;
    }

    public function getOutputHandler(): OutputHandlerInterface
    {
        return $this->outputHandler;
    }

    private function classToFileName(string $className): string
    {
        return str_replace(
            ['\\', self::NAMESPACE_BASE],
            ['/', $this->getRootDir()],
            $className
        );
    }

    /**
     * Generic class autoloader.
     *
     * @param string $className The class to load.
     */
    public function genericClassloader(string $className): void
    {
        $fileName = $this->classToFileName($className);
        if (is_file($fileName) && is_readable($fileName)) {
            /** @noinspection PhpIncludeInspection */
            require_once $fileName;
        }
    }

    /**
     * An error handler for uncaught application level exceptions.
     *
     * @param int    $errorNumber  The error number of the occurring exception.
     * @param string $errorMessage The error message of the occurring exception.
     * @param string $file         The file where the exception occurred.
     * @param int    $lineNumber   The line number where the exception occurred.
     */
    public function genericErrorHandler(
        int $errorNumber,
        string $errorMessage,
        string $file,
        int $lineNumber
    ): void {
        if ((error_reporting() & (int)$errorNumber) === 0) {
            return;
        }

        $this->outputHandler->write(sprintf(
            "Exception caught by %s.\n\nFile: %s:%d\nMessage: %s",
            __FUNCTION__,
            $file,
            $lineNumber,
            $errorMessage
        ));

        exit($errorNumber ?: 32);
    }
}
