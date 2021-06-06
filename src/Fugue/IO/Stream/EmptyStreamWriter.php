<?php

declare(strict_types=1);

namespace Fugue\IO\Stream;

final class EmptyStreamWriter implements StreamWriterInterface
{
    public function write(string $string): int
    {
        return 0;
    }

    public function close(): void
    {
    }
}
