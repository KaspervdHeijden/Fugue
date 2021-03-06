<?php

declare(strict_types=1);

namespace Fugue\Core\ClassLoader;

use Fugue\IO\Filesystem\FileSystemInterface;

use function class_exists;
use function str_replace;

final class DefaultClassLoader implements ClassLoaderInterface
{
    private const FILE_NAME_EXTENSION = '.php';

    private FileSystemInterface $fileSystem;
    private string $rootDir;

    public function __construct(FileSystemInterface $fileSystem, string $rootDir)
    {
        $this->fileSystem    = $fileSystem;
        $this->rootDir       = $rootDir;
    }

    private function classNameToFileName(string $className): string
    {
        return $this->rootDir . str_replace(
            ['\\'],
            ['/'],
            $className
        ) . self::FILE_NAME_EXTENSION;
    }

    public function loadClass(string $className): void
    {
        $fileName = $this->classNameToFileName($className);
        if ($this->fileSystem->isReadableFile($fileName)) {
            (static function (string $fileName): void {
                /** @noinspection PhpIncludeInspection */
                require_once $fileName;
            })($fileName);
        }
    }

    public function exists(string $className, bool $autoload): bool
    {
        return class_exists($className, $autoload);
    }
}
