<?php

declare(strict_types=1);

namespace Fugue\IO;

use RuntimeException;

final class IOException extends RuntimeException
{
    public static function forWritingToStream(): self
    {
        return new self('Could not write to stream');
    }

    public static function forWritingToClosedStream(): self
    {
        return new self('Could not write to closed stream');
    }

    public static function forWritingToFilename(string $filename): self
    {
        return new self("Could not write to '{$filename}'");
    }

    public static function forOpeningStream(string $filename): self
    {
        return new self("Could not open '{$filename}' for writing");
    }
}
