<?php

declare(strict_types=1);

namespace Fugue\Core\Output;

use Fugue\Core\IO\FileWriter;

final class FileOutputHandler implements OutputHandlerInterface
{
    /** @var FileWriter */
    private $fileWriter;

    /** @var resource|null */
    private $handle;

    public function __construct(FileWriter $fileWriter)
    {
        $this->fileWriter = $fileWriter;
    }

    public function getFilename(): string
    {
        return $this->fileWriter->getFilename();
    }

    public function write(string $text): void
    {
        $this->fileWriter->write($text);
    }
}
