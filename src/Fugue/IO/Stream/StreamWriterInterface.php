<?php

declare(strict_types=1);

namespace Fugue\IO\Stream;

interface StreamWriterInterface
{
    public function write(string $string): int;

    public function close(): void;
}
