<?php

declare(strict_types=1);

namespace Fugue\Logging;

use Fugue\Core\IO\FileWriter;

final class FileLogger extends Logger
{
    /** @var FileWriter */
    private $fileWriter;

    public function __construct(FileWriter $fileWriter)
    {
        $this->fileWriter = $fileWriter;
    }

    /**
     * Gets the filename this logger logs to.
     *
     * @return string The filename for this FileLogger.
     */
    public function getFilename(): string
    {
        return $this->fileWriter->getFilename();
    }

    protected function log(string $logType, string $message): void
    {
        $formattedMessage = $this->getFormattedMessage($logType, $message);
        $this->fileWriter->write($formattedMessage);
    }
}
