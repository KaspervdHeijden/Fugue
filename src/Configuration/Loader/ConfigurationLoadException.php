<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use Fugue\Core\Exception\FugueException;

final class ConfigurationLoadException extends FugueException
{
    public static function notSupportedIdentifier(string $className, string $identifier): self
    {
        return new static("{$className} does not support '{$identifier}'.");
    }

    public static function configurationNotIterable(string $identifier): self
    {
        return new static("Couldn't load configuration file for '{$identifier}'.");
    }
}
