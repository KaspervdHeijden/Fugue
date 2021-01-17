<?php

declare(strict_types=1);

namespace Fugue\Logging;

use Fugue\IO\Stream\StreamWriterInterface;

final class StreamLogger extends Logger
{
    private StreamWriterInterface $streamWriter;

    public function __construct(StreamWriterInterface $streamWriter)
    {
        $this->streamWriter = $streamWriter;
    }

    protected function log(string $logType, string $message): void
    {
        $formattedMessage = $this->getFormattedMessage($logType, $message);
        $this->streamWriter->write($formattedMessage);
    }
}
