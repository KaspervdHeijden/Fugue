<?php

declare(strict_types=1);

namespace Fugue\Core\IO;

final class EmptyWriter implements WriterInterface
{
    public function write(string $string)
    {
    }

    public function close(): void
    {
    }
}
