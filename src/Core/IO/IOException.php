<?php

declare(strict_types=1);

namespace Fugue\Core\IO;

use RuntimeException;

final class IOException extends RuntimeException
{
    public static function forWritingToFilename(string $filename): self
    {
        return new self("Could not write to '{$filename}'");
    }

    public static function forOpeningFilename(string $filename): self
    {
        return new self("Could not open '{$filename}' for writing");
    }
}
