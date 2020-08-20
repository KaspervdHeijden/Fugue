<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use Fugue\Configuration\ConfigurationNotFoundException;
use Fugue\Collection\Collection;

final class MultiConfigurationLoader implements ConfigurationLoaderInterface
{
    private array $configLoaders;

    public function __construct(ConfigurationLoaderInterface ...$configLoaders)
    {
        $this->configLoaders = $configLoaders;
    }

    public function supports(string $identifier): bool
    {
        foreach ($this->configLoaders as $loader) {
            if ($loader->supports($identifier)) {
                return true;
            }
        }

        return false;
    }

    public function load(string $identifier): Collection
    {
        foreach ($this->configLoaders as $loader) {
            if ($loader->supports($identifier)) {
                return $loader->load($identifier);
            }
        }

        throw ConfigurationNotFoundException::forIdentifier($identifier);
    }
}
