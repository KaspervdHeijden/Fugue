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
use Fugue\Container\Container;
use Fugue\HTTP\Request;
use Fugue\Core\Kernel;

require_once __DIR__ . '/../src/bootstrap.inc.php';

(new class() extends Kernel
{
    final protected function createRuntime(Container $container): RuntimeInterface
    {
        return new HttpRuntime(
            new NativeHttpResponseHeadersHandler(),
            $container->resolve(RouteCollectionMap::class),
            $container,
            $this
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
