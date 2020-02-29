<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use const INI_SCANNER_TYPED;
use function parse_ini_file;
use function is_array;

final class IniConfigurationLoader extends FileConfigurationLoader
{
    protected function getFullPathForIdentifier(
        string $directory,
        string $identifier
    ): string {
        return "{$directory}/ini/{$identifier}.conf.ini";
    }

    protected function loadFromFile(string $fileName): ?iterable
    {
        $results = parse_ini_file($fileName, true, INI_SCANNER_TYPED);
        if (! is_array($results)) {
            return null;
        }

        return $results;
    }
}
