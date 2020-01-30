<?php

declare(strict_types=1);

namespace Fugue\Core;

use Fugue\Configuration\Loader\ConfigurationLoaderInterface;
use Fugue\Configuration\Loader\PHPConfigurationLoader;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Collection\ArrayMap;
use Fugue\Container\Container;

use function date_default_timezone_set;
use function spl_autoload_extensions;
use function spl_autoload_register;
use function mb_internal_encoding;
use function mb_regex_encoding;
use function set_error_handler;
use function error_reporting;
use function mb_language;
use function str_replace;
use function is_readable;
use function is_iterable;
use function setlocale;
use function sprintf;
use function ini_set;
use function is_file;

final class Kernel
{
    /** @var string */
    private const NAMESPACE_BASE = 'Fugue';

    /** @var Container */
    private $container;

    /**
     * Instantiates the framework.
     *
     * Although this is not static, Fugue does NOT support multiple instances of the FrameWork.
     *
     * @param bool $debugMode Whether or not to run in debug mode.
     */
    public function __construct(bool $debugMode)
    {
        ini_set('display_errors', ($debugMode ? '1' : '0'));
        set_error_handler([$this, 'genericErrorHandler']);
        ini_set('zlib.output_compression', '1');
        error_reporting(E_ALL);

        spl_autoload_extensions('.php');
        spl_autoload_register([$this, 'genericClassloader']);

        // Set locale/timezone/charset
        ini_set('default_charset', RuntimeInterface::CHARSET);
        date_default_timezone_set(RuntimeInterface::CHARSET);
        setlocale(LC_TIME, RuntimeInterface::CHARSET);

        mb_internal_encoding(RuntimeInterface::CHARSET);
        mb_regex_encoding(RuntimeInterface::CHARSET);
        mb_language('uni');
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
     * @return ArrayMap          Result returned from the included file/
     */
    public function loadConfiguration(string $identifier): ArrayMap
    {
        $loaders = $this->getConfigurationLoaders();
        foreach ($loaders as $loader) {
            if (! $loader->supports($identifier)) {
                continue;
            }

            $result = $loader->load($identifier);
            if (is_iterable($result)) {
                return new ArrayMap($result);
            }
        }

        return new ArrayMap();
    }

    public function getContainer(): Container
    {
        if (! $this->container instanceof Container) {
            $services        = $this->loadConfiguration('services');
            $this->container = new Container(...$services->all());
        }

        return $this->container;
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

        echo sprintf(
            "Exception caught by %s.\n\nFile: %s:%d\nMessage: %s",
            __FUNCTION__,
            $file,
            $lineNumber,
            $errorMessage
        );

        exit($errorNumber ?: 32);
    }
}
