<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use function is_iterable;

final class PHPConfigurationLoader extends FileConfigurationLoader
{
    protected function getFullPathForIdentifier(
        string $directory,
        string $identifier
    ): string {
        return "{$directory}/php/{$identifier}.conf.php";
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
