<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use Fugue\Collection\CollectionMap;

use function is_readable;
use function is_iterable;
use function is_file;

abstract class FileConfigurationLoader implements ConfigurationLoaderInterface
{
    /** @var string */
    private $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    private function getFullPathForIdentifier(string $identifier): string
    {
        return "{$this->directory}/{$identifier}{$this->getFilenameSuffix()}";
    }

    /**
     * Loads the configuration from disk.
     *
     * @param string $fileName The filename to load.
     * @return iterable|null   Should return an iterable, or NULL on failure.
     */
    abstract protected function loadConfigurationFromFile(string $fileName): ?iterable;

    /**
     * The suffix of the file recognized by the implementation.
     */
    abstract protected function getFilenameSuffix(): string;

    public function supports(string $identifier): bool
    {
        $fileName = $this->getFullPathForIdentifier($identifier);
        return is_file($fileName) && is_readable($fileName);
    }

    public function load(string $identifier): CollectionMap
    {
        if (! $this->supports($identifier)) {
            throw ConfigurationLoadException::notSupportedIdentifier(
                static::class,
                $identifier
            );
        }

        $fileName = $this->getFullPathForIdentifier($identifier);
        $results  = $this->loadConfigurationFromFile($fileName);

        if (! is_iterable($results)) {
            throw ConfigurationLoadException::configurationNotIterable($identifier);
        }

        return new CollectionMap($results);
    }
}
