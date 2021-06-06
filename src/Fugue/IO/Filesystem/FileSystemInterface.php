<?php

declare(strict_types=1);

namespace Fugue\IO\Filesystem;

interface FileSystemInterface
{
    public function isDir(string $directory): bool;

    public function isFile(string $file): bool;

    public function isReadableDir(string $directory): bool;

    public function isReadableFile(string $file): bool;

    public function isExecutable(string $file): bool;
}
