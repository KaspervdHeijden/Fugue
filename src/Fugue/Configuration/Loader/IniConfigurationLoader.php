<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use function parse_ini_file;
use function is_array;

use const INI_SCANNER_TYPED;

final class IniConfigurationLoader extends FileConfigurationLoader
{
    protected function loadFromFile(string $filename): ?array
    {
        $results = parse_ini_file($filename, true, INI_SCANNER_TYPED);
        if (! is_array($results)) {
            return null;
        }

        return $results;
    }
}
