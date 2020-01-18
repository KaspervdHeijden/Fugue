<?php

declare(strict_types=1);

/**
 * Fugue front controller
 *
 * _______  __    __    _______  __    __   _______
 * |   ____||  |  |  |  /  _____||  |  |  | |   ____|
 * |  |__   |  |  |  | |  |  __  |  |  |  | |  |__
 * |   __|  |  |  |  | |  | |_ | |  |  |  | |   __|
 * |  |     |  `--'  | |  |__| | |  `--'  | |  |____
 * |__|      \______/   \______|  \______/  |_______|
 *
 * (c) 2020. All rights reserved.
 */

use Fugue\Core\RuntimeFactory;
use Fugue\Core\FrameWork;
use Fugue\HTTP\Request;

require_once __DIR__ . '/src/bootstrap.inc.php';

(new RuntimeFactory())->getRuntime(new FrameWork(false))
                      ->handle(Request::fromSuperGlobals());
