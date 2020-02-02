<?php

declare(strict_types=1);

namespace Fugue\Core\Output;

interface OutputHandlerInterface
{
    public function write(string $text);

    public function writeLine(string $text);
}
