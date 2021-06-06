<?php

declare(strict_types=1);

namespace Fugue\IO\Stream;

use InvalidArgumentException;
use Fugue\IO\IOException;

use function is_resource;

class StreamStreamWriter implements StreamWriterInterface
{
    /** @var resource|null */
    private $stream;

    public function __construct($stream)
    {
        if (! is_resource($stream)) {
            throw new InvalidArgumentException('Invalid stream');
        }

        $this->stream = $stream;
    }

    public function __destruct()
    {
        $this->close();
    }

    public function write(string $string): int
    {
        if (! is_resource($this->stream)) {
            throw IOException::forWritingToClosedStream();
        }

        $written = fwrite($this->stream, $string);
        if (! is_int($written)) {
            throw IOException::forWritingToStream();
        }

        return $written;
    }

    public function close(): void
    {
        if (is_resource($this->stream)) {
            fclose($this->stream);
            $this->stream = null;
        }
    }

    public static function forStdOut(): self
    {
        return new self(STDOUT);
    }

    public static function forStdErr(): self
    {
        return new self(STDERR);
    }
}
