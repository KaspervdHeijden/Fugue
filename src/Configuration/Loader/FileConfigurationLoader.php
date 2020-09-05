<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use Fugue\Collection\CollectionMap;
use Fugue\Collection\Collection;

use function is_readable;
use function is_iterable;
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

    /**
     * Loads the configuration from disk.
     *
     * @param string $filename The filename to load.
     * @return iterable|null   Should return an iterable, or NULL on failure.
     */
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

        return (bool)$pathInfo->success;
    }

    public function load(string $identifier): Collection
    {
        $pathInfo = $this->getPathInfoForIdentifier($this->directory, $identifier);
        if (! (bool)$pathInfo->success) {
            throw ConfigurationLoadException::notSupportedIdentifier(
                static::class,
                $identifier
            );
        }

        $results = $this->loadFromFile((string)$pathInfo->filename);
        if (! is_iterable($results)) {
            throw ConfigurationLoadException::configurationNotIterable($identifier);
        }

        return new CollectionMap($results);
    }
}
