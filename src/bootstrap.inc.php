<?php

declare(strict_types=1);

if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    include_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/Fugue/Core/ClassLoader/ClassLoaderInterface.php';
require_once __DIR__ . '/Fugue/Core/ClassLoader/DefaultClassLoader.php';
require_once __DIR__ . '/Fugue/IO/Filesystem/FileSystemInterface.php';
require_once __DIR__ . '/Fugue/IO/Filesystem/NativeFileSystem.php';
require_once __DIR__ . '/Fugue/Core/Kernel.php';
