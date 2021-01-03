<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use function is_readable;
use function is_array;
use function is_file;
use function is_dir;

abstract class FileConfigurationLoader implements ConfigurationLoaderInterface
{
    private string $directory;
    private string $name;

    public function __construct(string $directory, string $name)
    {
        if (! is_dir($directory)) {
            throw ConfigurationLoadException::invalidSourceDirectory($directory);
        }

        $this->directory = $directory;
        $this->name      = $name;
    }

    abstract protected function loadFromFile(string $filename): ?iterable;

    private function getPathInfoForIdentifier(
        string $directory,
        string $identifier
    ): object {
        $fileNames = [
            "{$directory}/{$this->name}/{$identifier}.conf.{$this->name}.env",
            "{$directory}/{$this->name}/{$identifier}.conf.{$this->name}",
        ];

        foreach ($fileNames as $fileName) {
            if (is_file($fileName) && is_readable($fileName)) {
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
