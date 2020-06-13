<?php

declare(strict_types=1);

namespace Fugue\Logging;

use Fugue\Core\IO\StreamWriter;

final class FileLogger extends Logger
{
    private StreamWriter $streamWriter;

    public function __construct(StreamWriter $streamWriter)
    {
        $this->streamWriter = $streamWriter;
    }

    /**
     * Gets the filename this logger logs to.
     *
     * @return string The filename for this FileLogger.
     */
    public function getFilename(): string
    {
        return $this->streamWriter->getFilename();
    }

    protected function log(string $logType, string $message): void
    {
        $formattedMessage = $this->getFormattedMessage($logType, $message);
        $this->streamWriter->write($formattedMessage);
    }
}
