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

use Fugue\Core\Output\StandardOutputHandler;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\Collection\PropertyBag;
use Fugue\Core\RuntimeFactory;
use Fugue\HTTP\Request;
use Fugue\Core\Kernel;

require_once __DIR__ . '/src/bootstrap.inc.php';

ini_set('default_charset', RuntimeInterface::CHARSET);
ini_set('error_reporting', (string)E_ALL);
ini_set('display_errors', '1');

mb_internal_encoding(RuntimeInterface::CHARSET);
mb_regex_encoding(RuntimeInterface::CHARSET);
mb_http_output(RuntimeInterface::CHARSET);
mb_http_input(RuntimeInterface::CHARSET);
mb_language('uni');

(new RuntimeFactory())
    ->getRuntime(new Kernel(new StandardOutputHandler(), true))
    ->handle(new Request(
        new PropertyBag($_GET),
        new PropertyBag($_POST),
        new PropertyBag($_COOKIE),
        new PropertyBag($_FILES),
        new PropertyBag($_SERVER)
    ));
