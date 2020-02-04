<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use function is_iterable;

final class PHPConfigurationLoader extends FileConfigurationLoader
{
    private const FILENAME_SUFFIX = '.php';

    protected function getFilenameSuffix(): string
    {
        return self::FILENAME_SUFFIX;
    }

    protected function loadConfigurationFromFile(string $fileName): ?iterable
    {
        /** @noinspection PhpIncludeInspection */
        $result = require_once $fileName;
        switch (true) {
            case $result === null:
                return null;
            case is_iterable($result):
                return $result;
            default:
                return [$result];
        }
    }
}
