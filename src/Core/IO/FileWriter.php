<?php

declare(strict_types=1);

namespace Fugue\Core\IO;

final class FileWriter
{
    /** @var WriterInterface */
    private $writer;

    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    public function write(string $text): void
    {
        $this->writer->write($text);
    }

    public function close(): void
    {
        $this->writer->close();
    }

    public function __destruct()
    {
        $this->close();
    }
}
