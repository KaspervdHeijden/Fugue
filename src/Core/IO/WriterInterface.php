<?php

declare(strict_types=1);

namespace Fugue\Core\IO;

interface WriterInterface
{
    public function write(string $string);

    public function close(): void;
}
