#!/usr/bin/env php
<?php

declare(strict_types=1);

/**
 * Fugue CLI controller
 * _______  __    __    _______  __    __   _______
 * |   ____||  |  |  |  /  _____||  |  |  | |   ____|
 * |  |__   |  |  |  | |  |  __  |  |  |  | |  |__
 * |   __|  |  |  |  | |  | |_ | |  |  |  | |   __|
 * |  |     |  `--'  | |  |__| | |  `--'  | |  |____
 * |__|      \______/   \______|  \______/  |_______|
 *
 * (c) 2020. All rights reserved.
 */

use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Core\Runtime\CLIRuntime;
use Fugue\Command\CommandFactory;
use Fugue\Core\FrontController;

require_once __DIR__ . '/../src/bootstrap.inc.php';

(new class(E_ALL, 'utf-8', true) extends FrontController
{
    protected function createRuntime(): RuntimeInterface
    {
        $factory = new CommandFactory(
            $this->getClassResolver(),
            $this->getKernel()->getContainer()
        );

        return new CLIRuntime($factory);
    }
})->handleRequest([], [], [], [], $_SERVER);
