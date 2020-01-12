<?php

declare(strict_types=1);

$rootDir = rtrim(realpath(__DIR__ . '/../'), '/') . '/';
return [
    // Database
    'database' => [
        'databaseName' => '<your-database-name>',
        'userName'     => '<your-user-name>',
        'password'     => '<your-password>',
        'host'         => '<your-host>',
        'identifier'   => 'pdo-mysql',
    ],

    // Session
    'session' => [
        'name'       => 'vc_sess_id',  // The session name
        'identifier' => 'native',
        'timeout'    => 14400,         // Log out after 4 hours of no activity
    ],

    // Set locale to Dutch
    'localization' => [
        'timezone'   => 'Europe/Amsterdam',
        'identifier' => 'dutch',
        'locale'     => 'nl_NL',
    ],

    // Application
    'application' => [
        'version'       => '<your-application-version>',
        'name'          => '<your-application-name>',
        'default-email' => '',
        'url-prefix'    => '',
    ],

    // Templating
    'templating' => [
        'identifier' => 'php',
    ],

    // Directory structure settings. Important! These paths must ALWAYS end with a trailing slash!
    'directory' => [
        'templates'   => $rootDir . 'templates/',
        'assets'      => $rootDir . 'assets/',
        'root'        => $rootDir,
    ],

    // Imaging
    'imaging' => [
        'max-resolution' => 225 * 225, // ~50k
        'max-size'       => 200,
    ],

    // Logger classes
    'logging' => [
        'default-logger-identifier' => 'blackhole',
        'debug-logger-identifier'   => 'output',
    ],
];
