<?php

declare(strict_types=1);

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/Core/ClassLoader/ClassLoaderInterface.php';
require_once __DIR__ . '/Core/ClassLoader/DefaultClassLoader.php';
require_once __DIR__ . '/IO/Filesystem/FileSystemInterface.php';
require_once __DIR__ . '/IO/Filesystem/NativeFileSystem.php';
require_once __DIR__ . '/Core/FrontController.php';
