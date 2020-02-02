<?php

declare(strict_types=1);

namespace Fugue\Core\Output;

final class StandardOutputHandler implements OutputHandlerInterface
{
    public function writeLine(string $text)
    {
        $this->write($text . PHP_EOL);
    }

    public function write(string $text)
    {
        echo $text;
    }
}
