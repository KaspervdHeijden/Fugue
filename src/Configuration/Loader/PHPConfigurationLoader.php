<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use function is_iterable;

final class PHPConfigurationLoader extends FileConfigurationLoader
{
    protected function loadFromFile(string $filename): ?iterable
    {
        /** @noinspection PhpIncludeInspection */
        $result = require_once $filename;
        switch (true) {
            case ($result === null):
                return null;
            case is_iterable($result):
                return $result;
            default:
                return (array)$result;
        }
    }
}
