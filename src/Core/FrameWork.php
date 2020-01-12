<?php

declare(strict_types=1);

namespace Fugue\Core;

use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Configuration\Config;

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
use function setlocale;
use function sprintf;
use function ini_set;

final class FrameWork
{
    /**
     * @var int The process exit code in case of an exception.
     */
    private const EXIT_CODE_EXCEPTION = 1;

    /**
     * @var string The class namespaces root.
     */
    private const NAMESPACE_BASE      = 'Fugue';

    /** @var Config */
    private $config;

    /**
     * Instantiates the framework.
     *
     * Although this is not static, Fugue does NOT support multiple instances of the FrameWork.
     */
    public function __construct()
    {
        set_error_handler([$this, 'genericErrorHandler']);

        ini_set('zlib.output_compression', '1');
        ini_set('display_errors', '1'); // '0'
        error_reporting(E_ALL);         // 0

        spl_autoload_extensions('.php');
        spl_autoload_register([$this, 'genericClassloader']);

        $localization = $this->getConfig()->getBranch('localization');

        // Set locale/timezone/charset
        ini_set('default_charset', RuntimeInterface::CHARSET);
        date_default_timezone_set(RuntimeInterface::CHARSET);
        setlocale(LC_TIME, RuntimeInterface::CHARSET);

        mb_internal_encoding($localization['charset']);
        mb_regex_encoding($localization['charset']);
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
     * @param string $fileName
     * @return mixed|null
     */
    private function requireOnce(string $fileName)
    {
        if (is_readable($fileName)) {
            /** @noinspection PhpIncludeInspection */
            return require_once $fileName;
        }

        return null;
    }

    /**
     * Loads a configuration file.
     *
     * @param string $fileName The filename to load.
     * @return array           Result returned from the included file, or NULL
     */
    public function loadConfigFile(string $fileName): array
    {
        $result = $this->requireOnce("{$this->getRootDir()}../conf/{$fileName}.inc.php");
        if ($result === null) {
            return [];
        }

        return (array)$result;
    }

    public function getConfig(): Config
    {
        if (! $this->config instanceof Config) {
            $this->config = new Config(
                $this->loadConfigFile('config'),
                $this->loadConfigFile('env.config')
            );
        }

        return $this->config;
    }

    /**
     * Generic class autoloader.
     *
     * @param string $className The class to load.
     */
    public function genericClassloader(string $className): void
    {
        $this->requireOnce(str_replace(
            ['\\', self::NAMESPACE_BASE],
            ['/', $this->getRootDir()],
            $className
        ));
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

        exit($errorNumber ?: self::EXIT_CODE_EXCEPTION);
    }
}
