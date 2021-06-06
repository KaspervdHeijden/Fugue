<?php

declare(strict_types=1);

namespace Fugue\IO\Filesystem;

use function is_executable;
use function is_readable;
use function is_file;
use function is_dir;

final class NativeFileSystem implements FileSystemInterface
{
    public function isDir(string $directory): bool
    {
        return $directory !== '' && is_dir($directory);
    }

    public function isFile(string $file): bool
    {
        return $file !== '' && is_file($file);
    }

    public function isReadableDir(string $directory): bool
    {
        return $this->isDir($directory) && is_readable($directory);
    }

    public function isReadableFile(string $file): bool
    {
        return $this->isFile($file) && is_readable($file);
    }

    public function isExecutable(string $file): bool
    {
        return $file !== '' && is_executable($file);
    }
}
