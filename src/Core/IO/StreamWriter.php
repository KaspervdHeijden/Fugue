<?php

declare(strict_types=1);

namespace Fugue\Core\IO;

use InvalidArgumentException;

use function is_resource;
use function in_array;
use function is_int;
use function fwrite;
use function fclose;
use function fopen;

final class StreamWriter implements WriterInterface
{
    /** @var string */
    public const DEFAULT_MODE = 'w';

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

    private string $filename;
    private string $mode;

    /** @var resource|null */
    private $handle;

    public function __construct(
        string $filename,
        string $mode = self::DEFAULT_MODE
    ) {
        if (! in_array($mode, self::VALID_MODES, true)) {
            throw new InvalidArgumentException(
                "Invalid file opening mode ({$mode}) for logger file."
            );
        }

        if ($filename === '') {
            throw new InvalidArgumentException('Filename should not be empty.');
        }

        $this->filename = $filename;
        $this->mode     = $mode;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * Gets the File open mode.
     *
     * @return string  The file open mode.
     * @see    fopen() For possible values.
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * @return resource The file handle
     */
    private function getHandle()
    {
        if (! is_resource($this->handle)) {
            $handle = fopen($this->filename, $this->mode);

            if (! is_resource($handle)) {
                throw IOException::forOpeningFilename($this->filename);
            }

            $this->handle = $handle;
        }

        return $this->handle;
    }

    public function close(): void
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    public function write(string $string): int
    {
        $handle  = $this->getHandle();
        $written = fwrite($handle, $string);

        if (! is_int($written)) {
            throw IOException::forWritingToFilename($this->filename);
        }

        return $written;
    }
}
