<?php

declare(strict_types=1);

namespace Fugue\Logging;

use InvalidArgumentException;
use RuntimeException;

use function mb_strtolower;
use function is_resource;
use function in_array;
use function fwrite;
use function fclose;
use function fopen;

final class FileLogger extends Logger
{
    /** @var string */
    public const DEFAULT_MODE = 'a';

    /** @var string[] */
    public const VALID_MODES = [
        'a+',
        'x+',
        'w+',
        'c+',
        'a',
        'x',
        'w',
        'c',
    ];

    /** @var resource|null */
    private $filePointer;

    /** @var string */
    private $filename;

    /** @var string */
    private $mode;

    public function __construct(
        string $filename,
        string $mode = self::DEFAULT_MODE
    ) {
        $this->setFilename($filename);
        $this->setMode($mode);
    }

    /**
     * Gets the filename this logger logs to.
     *
     * @return string The filename for this FileLogger.
     */
    public function getFilename(): string
    {
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

        if ($filename === '') {
            throw new InvalidArgumentException(
                'Empty filename not allowed.'
            );
        }

        $this->filename = $filename;
    }

    /**
     * Sets the FileOpen mode.
     *
     * @param string $mode The file open mode. Must be w, w+, a, a+, x, x+, c or c+.
     * @see   fopen()      For values of $mode.
     */
    public function setMode(string $mode): void
    {
        $value = mb_strtolower((string)$mode);
        if (! in_array($value, self::VALID_MODES, true)) {
            throw new InvalidArgumentException(
                "Invalid file opening mode ({$mode}) for logger file."
            );
        }

        $this->mode = $value;
    }

    /**
     * Gets the FileOpen mode.
     *
     * @return string The file open mode.
     * @see    fopen() for values of $mode.
     */
    public function getMode(): string
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
            $pointer = fopen($this->filename, $this->mode);

            if (! is_resource($pointer)) {
                throw new RuntimeException(
                    "Could not open '{$this->filename}' for logging."
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
        if (is_resource($this->filePointer)) {
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
        $formattedMessage = $this->getFormattedMessage($logType, $message);
        $resource         = $this->getFilePointer();

        if (fwrite($resource, $formattedMessage) === false) {
            throw new RuntimeException(
                "Could not write to '{$this->filename}'."
            );
        }
    }
}
