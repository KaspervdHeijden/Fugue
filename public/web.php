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

use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Core\Runtime\HttpRuntime;
use Fugue\Core\FrontController;

require_once __DIR__ . '/../src/bootstrap.inc.php';

(new class(E_ALL, 'utf-8', true) extends FrontController
{
    protected function createRuntime(): RuntimeInterface
    {
        $container = $this->getKernel()->getContainer();
        return new HttpRuntime(
            $this->getOutputHandler(),
            $container->resolve(RouteCollectionMap::class),
            $container
        );
    }
})->handleRequest($_GET, $_POST, $_COOKIE, $_FILES, $_SERVER);
