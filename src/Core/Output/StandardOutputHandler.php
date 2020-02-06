<?php

declare(strict_types=1);

namespace Fugue\Core\Output;

final class StandardOutputHandler implements OutputHandlerInterface
{
    public function write(string $text): void
    {
        echo $text;
    }
}
