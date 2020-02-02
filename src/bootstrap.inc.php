<?php

declare(strict_types=1);

/**
 * _______  __    __    _______  __    __   _______
 * |   ____||  |  |  |  /  _____||  |  |  | |   ____|
 * |  |__   |  |  |  | |  |  __  |  |  |  | |  |__
 * |   __|  |  |  |  | |  | |_ | |  |  |  | |   __|
 * |  |     |  `--'  | |  |__| | |  `--'  | |  |____
 * |__|      \______/   \______|  \______/  |_______|
 *
 * (c) 2020. All rights reserved.
 */

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/Core/Output/OutputHandlerInterface.php';
require_once __DIR__ . '/Core/Output/StandardOutputHandler.php';
require_once __DIR__ . '/Core/Runtime/RuntimeInterface.php';
require_once __DIR__ . '/Core/RuntimeFactory.php';
require_once __DIR__ . '/Core/Kernel.php';
