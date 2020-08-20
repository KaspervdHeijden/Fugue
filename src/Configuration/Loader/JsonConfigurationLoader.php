<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use function file_get_contents;
use function json_decode;
use function is_array;

final class JsonConfigurationLoader extends FileConfigurationLoader
{
    protected function loadFromFile(string $filename): ?iterable
    {
        $contents = file_get_contents($filename);
        $json     = json_decode($contents);

        if (! is_array($json)) {
            return null;
        }

        return $json;
    }
}
