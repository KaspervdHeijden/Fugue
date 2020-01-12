<?php

declare(strict_types=1);

namespace Fugue\Persistence\Repository;

use Fugue\Persistence\IO\FileSystemUtil;

abstract class FilesystemRepository
{
    /** @var FileSystemUtil */
    private $fileSystemUtil;

    final protected function getFileSystemUtil(): FileSystemUtil
    {
        if (! $this->fileSystemUtil instanceof FileSystemUtil) {
            $this->fileSystemUtil = new FileSystemUtil();
        }

        return $this->fileSystemUtil;
    }
}
