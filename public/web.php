<?php

declare(strict_types=1);

/**
 * Fugue HTTP handler controller
 * _______  __    __    _______  __    __   _______
 * |   ____||  |  |  |  /  _____||  |  |  | |   ____|
 * |  |__   |  |  |  | |  |  __  |  |  |  | |  |__
 * |   __|  |  |  |  | |  | |_ | |  |  |  | |   __|
 * |  |     |  `--'  | |  |__| | |  `--'  | |  |____
 * |__|      \______/   \______|  \______/  |_______|
 */

use Fugue\HTTP\NativeHttpResponseHeadersHandler;
use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Core\Runtime\HttpRuntime;
use Fugue\Container\ClassResolver;
use Fugue\Core\FrontController;
use Fugue\Container\Container;
use Fugue\HTTP\Request;
use Fugue\Core\Kernel;

require_once __DIR__ . '/../src/bootstrap.inc.php';

(new class(E_ALL, 'utf-8', true) extends FrontController
{
    final protected function createRuntime(
        Kernel $kernel,
        Container $container,
        ClassResolver $classResolver
    ): RuntimeInterface {
        return new HttpRuntime(
            new NativeHttpResponseHeadersHandler(),
            $kernel->getOutputHandler(),
            $kernel->getClassLoader(),
            $classResolver,
            $container->resolve(RouteCollectionMap::class),
            $container,
        );
    }
})->handleRequest(Request::fromArrays(
    $_SERVER,
    $_ENV,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
));
