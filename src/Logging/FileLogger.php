<?php

declare(strict_types=1);

namespace Fugue\Logging;

use InvalidArgumentException;
use RuntimeException;
use LogicException;

use function mb_strtolower;
use function is_resource;
use function fwrite;
use function fclose;
use function fopen;

final class FileLogger extends Logger
{
    /** @var resource|null */
    private $filePointer;

    /** @var string */
    private $filename = '';

    /** @var string */
    private $mode = 'a';

    /**
     * Gets the filename this logger logs to.
     *
     * @return string The filename for this FileLogger.
     */
    public function getFilename(): string
    {
        if ($this->filename === '') {
            throw new LogicException(
                'No filename given for FileLogger.'
            );
        }

        return $this->filename;
    }

    /**
     * Sets the filename this logger logs to.
     *
     * @param string $filename The filename to log to.
     */
    public function setFilename(string $filename): void
    {
        if ($filename !== $this->filename) {
            $this->close();
        }

        $this->filename = (string)$filename;
    }

    /**
     * Sets the FileOpen mode.
     *
     * @see   fopen()      For values of $mode.
     * @param string $mode The file open mode. Must be w, w+, a, a+, x, x+, c or c+.
     */
    public function setFileOpenMode(string $mode): void
    {
        $value = mb_strtolower((string)$mode);
        switch ($value) {
            case 'a+':
            case 'x+':
            case 'w+':
            case 'c+':
            case 'a':
            case 'x':
            case 'w':
            case 'c':
                break;
            default:
                throw new InvalidArgumentException(
                    "Invalid file opening mode ({$mode}) for logger file."
                );
        }

        $this->mode = $value;
    }

    /**
     * Gets the FileOpen mode.
     *
     * @see fopen() for values of $mode.
     * @return string The file open mode.
     */
    public function getFileOpenMode(): string
    {
        return $this->mode;
    }

    /**
     * Gets a pointer to the file.
     *
     * @return resource The file pointer
     */
    private function getFilePointer()
    {
        if (! is_resource($this->filePointer)) {
            $filename = $this->getFilename();
            $pointer  = fopen($filename, $this->getFileOpenMode());

            if (! is_resource($pointer)) {
                throw new RuntimeException(
                    "Could not open '{$filename}' for logging."
                );
            }

            $this->filePointer = $pointer;
        }

        return $this->filePointer;
    }

    /**
     * Closes the internal file pointer.
     */
    public function close(): void
    {
        if (! is_resource($this->filePointer)) {
            fclose($this->filePointer);
            $this->filePointer = null;
        }
    }

    public function __destruct()
    {
        $this->close();
    }

    protected function log(string $logType, string $message): void
    {
        fwrite($this->getFilePointer(), $this->getFormattedMessage($logType, $message));
    }
}
