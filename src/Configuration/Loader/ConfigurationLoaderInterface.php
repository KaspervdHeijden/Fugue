<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use Fugue\Collection\Collection;

interface ConfigurationLoaderInterface
{
    /**
     * Asks the implementation if the specified identifier is supported.
     */
    public function supports(string $identifier): bool;

    /**
     * Loads the configuration for the specified identifier.
     */
    public function load(string $identifier): Collection;
}
