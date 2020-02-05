<?php

declare(strict_types=1);

namespace Fugue\Configuration;

use Fugue\Core\Exception\FugueException;

final class ConfigurationNotFoundException extends FugueException
{
    public static function forIdentifier(string $identifier): self
    {
        return new static(
            "Could not load configuration for '{$identifier}'."
        );
    }
}
