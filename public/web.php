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
 *
 * (c) 2020. All rights reserved.
 */

use Fugue\HTTP\Routing\RouteCollectionMap;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Core\Runtime\HttpRuntime;
use Fugue\Collection\PropertyBag;
use Fugue\Core\FrontController;
use Fugue\HTTP\Request;

require_once __DIR__ . '/../src/bootstrap.inc.php';

(new class(E_ALL, 'utf-8', true) extends FrontController
{
    protected function createRuntime(): RuntimeInterface
    {
        $container = $this->getKernel()->getContainer();
        return new HttpRuntime(
            $this->getOutputHandler(),
            $container->resolve(RouteCollectionMap::class),
            $this->getClassResolver(),
            $container
        );
    }
})->handleRequest(new Request(
    new PropertyBag($_GET),
    new PropertyBag($_POST),
    new PropertyBag($_COOKIE),
    new PropertyBag($_FILES),
    new PropertyBag($_SERVER)
));
