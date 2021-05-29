<?php

declare(strict_types=1);

namespace Fugue\Core\Output;

use Fugue\IO\Stream\StreamWriterInterface;

final class StreamOutputHandler implements OutputHandlerInterface
{
    public StreamWriterInterface $streamWriter;

    public function __construct(StreamWriterInterface $streamWriter)
    {
        $this->streamWriter = $streamWriter;
    }

    public function write(string $text): void
    {
        $this->streamWriter->write($text);
    }
}
