<?php

declare(strict_types=1);

/**
 * Fugue Front controller
 * _______  __    __    _______  __    __   _______
 * |   ____||  |  |  |  /  _____||  |  |  | |   ____|
 * |  |__   |  |  |  | |  |  __  |  |  |  | |  |__
 * |   __|  |  |  |  | |  | |_ | |  |  |  | |   __|
 * |  |     |  `--'  | |  |__| | |  `--'  | |  |____
 * |__|      \______/   \______|  \______/  |_______|
 *
 * (c) 2020. All rights reserved.
 */

use Fugue\Configuration\Loader\{IniConfigurationLoader, PHPConfigurationLoader};
use Fugue\Core\ClassLoader\DefaultClassLoader;
use Fugue\Core\Output\StandardOutputHandler;
use Fugue\Core\Exception\OutputErrorHandler;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Core\{RuntimeFactory, Kernel};
use Fugue\Container\ContainerLoader;
use Fugue\Collection\PropertyBag;
use Fugue\HTTP\Request;

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/../src/Core/ClassLoader/ClassLoaderInterface.php';
require_once __DIR__ . '/../src/Core/ClassLoader/DefaultClassLoader.php';
require_once __DIR__ . '/../src/Core/Runtime/RuntimeInterface.php';

(new class(RuntimeInterface::CHARSET, 'uni', true)
{
    public function __construct(string $charset, string $language, bool $debug)
    {
        ini_set('display_errors', $debug ? '1' : '0');
        ini_set('error_reporting', (string)E_ALL);
        ini_set('default_charset', $charset);

        mb_internal_encoding($charset);
        mb_regex_encoding($charset);
        mb_http_output($charset);
        mb_http_input($charset);
        mb_language($language);
    }

    public function fugue(string $srcDir, string $configDir, string $rootNamespace): void
    {
        $classLoader   = new DefaultClassLoader($srcDir, $rootNamespace);
        $outputHandler = new StandardOutputHandler();
        $kernel        = new Kernel(
            $outputHandler,
            new OutputErrorHandler($outputHandler),
            $classLoader,
            new ContainerLoader([
                new IniConfigurationLoader($configDir),
                new PHPConfigurationLoader($configDir),
            ])
        );

        $request = new Request(
            new PropertyBag($_GET),
            new PropertyBag($_POST),
            new PropertyBag($_COOKIE),
            new PropertyBag($_FILES),
            new PropertyBag($_SERVER)
        );

        (new RuntimeFactory())
            ->getRuntime($kernel)
            ->handle($request);
    }
})->fugue(__DIR__ . '/../src', __DIR__ . '/../conf', 'Fugue');
