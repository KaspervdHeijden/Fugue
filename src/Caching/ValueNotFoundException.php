<?php

declare(strict_types=1);

namespace Fugue\Caching;

use Fugue\Core\Exception\FugueException;

final class ValueNotFoundException extends FugueException
{
    public static function forKey(string $key): self
    {
        return new self("Value not found for key '{$key}'.");
    }
}
