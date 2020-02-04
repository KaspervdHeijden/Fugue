<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use const INI_SCANNER_TYPED;
use function parse_ini_file;
use function is_array;

final class IniConfigurationLoader extends FileConfigurationLoader
{
    /** @var string */
    private const FILENAME_SUFFIX = '.ini';

    protected function getFilenameSuffix(): string
    {
        return self::FILENAME_SUFFIX;
    }

    protected function loadConfigurationFromFile(string $fileName): ?iterable
    {
        $results = parse_ini_file($fileName, true, INI_SCANNER_TYPED);
        if (! is_array($results)) {
            return null;
        }

        return $results;
    }
}
