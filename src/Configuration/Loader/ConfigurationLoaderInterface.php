<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use Fugue\Collection\CollectionMap;

interface ConfigurationLoaderInterface
{
    public function supports(string $identifier): bool;

    public function load(string $identifier): CollectionMap;
}
