<?php

declare(strict_types=1);

namespace Fugue\Core\Output;

final class NullOutputHandler implements OutputHandlerInterface
{
    public function write(string $text)
    {
    }

    public function writeLine(string $text)
    {
        $this->write($text);
    }
}
