<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

final class PHPConfigurationLoader extends FileConfigurationLoader
{
    protected function loadFromFile(string $filename): ?array
    {
        /** @noinspection PhpIncludeInspection */
        $result = require_once $filename;
        if ($result === null) {
            return null;
        }

        return (array)$result;
    }
}
