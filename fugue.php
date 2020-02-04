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

use Fugue\Configuration\Loader\PHPConfigurationLoader;
use Fugue\Core\ClassLoader\DefaultClassLoader;
use Fugue\Core\Output\StandardOutputHandler;
use Fugue\Core\Exception\OutputErrorHandler;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Collection\PropertyBag;
use Fugue\Core\RuntimeFactory;
use Fugue\HTTP\Request;
use Fugue\Core\Kernel;

if (is_file(__DIR__ . '/vendor/autoload.php')) {
    include_once __DIR__ . '/vendor/autoload.php';
}

require_once __DIR__ . '/src/Core/ClassLoader/ClassLoaderInterface.php';
require_once __DIR__ . '/src/Core/ClassLoader/DefaultClassLoader.php';
require_once __DIR__ . '/src/Core/Runtime/RuntimeInterface.php';

(new class(RuntimeInterface::CHARSET, 'uni', true)
{
    public function __construct(string $defaultCharset, string $language, bool $debugMode)
    {
        ini_set('display_errors', $debugMode ? '1' : '0');
        ini_set('default_charset', $defaultCharset);
        ini_set('error_reporting', (string)E_ALL);

        mb_internal_encoding($defaultCharset);
        mb_regex_encoding($defaultCharset);
        mb_http_output($defaultCharset);
        mb_http_input($defaultCharset);
        mb_language($language);
    }

    public function fugue(string $currentDir): void
    {
        $classLoader   = new DefaultClassLoader("{$currentDir}/src", 'Fugue');
        $outputHandler = new StandardOutputHandler();
        $kernel        = new Kernel(
            $outputHandler,
            new OutputErrorHandler($outputHandler, true),
            $classLoader,
            [new PHPConfigurationLoader("{$currentDir}/conf")]
        );

        $request = new Request(
            new PropertyBag($_GET),
            new PropertyBag($_POST),
            new PropertyBag($_COOKIE),
            new PropertyBag($_FILES),
            new PropertyBag($_SERVER)
        );

        (new RuntimeFactory())->getRuntime($kernel)
                              ->handle($request);
    }
})->fugue(__DIR__);
