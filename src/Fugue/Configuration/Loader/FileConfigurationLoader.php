<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use Fugue\IO\Filesystem\FileSystemInterface;

use function is_array;

abstract class FileConfigurationLoader implements ConfigurationLoaderInterface
{
    private FileSystemInterface $fileSystem;
    private string $directory;
    private string $name;

    public function __construct(
        FileSystemInterface $fileSystem,
        string $directory,
        string $name
    ) {
        if (! $fileSystem->isReadableDir($directory)) {
            throw ConfigurationLoadException::invalidSourceDirectory($directory);
        }

        $this->fileSystem = $fileSystem;
        $this->directory  = $directory;
        $this->name       = $name;
    }

    abstract protected function loadFromFile(string $filename): ?array;

    private function getPathInfoForIdentifier(string $directory, string $identifier): object
    {
        $fileNames = [
            "{$directory}/{$this->name}/{$identifier}.conf.{$this->name}.env",
            "{$directory}/{$this->name}/{$identifier}.conf.{$this->name}",
        ];

        foreach ($fileNames as $fileName) {
            if ($this->fileSystem->isReadableFile($fileName)) {
                return (object)['success' => true, 'filename' => $fileName];
            }
        }

        return (object)['success' => false, 'filename' => ''];
    }

    public function supports(string $identifier): bool
    {
        $pathInfo = $this->getPathInfoForIdentifier(
            $this->directory,
            $identifier
        );

        return $pathInfo->success;
    }

    public function load(string $identifier): array
    {
        $pathInfo = $this->getPathInfoForIdentifier($this->directory, $identifier);
        if (! $pathInfo->success) {
            throw ConfigurationLoadException::notSupportedIdentifier(
                static::class,
                $identifier
            );
        }

        $results = $this->loadFromFile($pathInfo->filename);
        if (! is_array($results)) {
            throw ConfigurationLoadException::configurationNotIterable($identifier);
        }

        return $results;
    }
}
