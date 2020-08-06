<?php

declare(strict_types=1);

namespace Fugue\Container;

interface ContainerDefinitionInterface
{
    /**
     * Resolves an object from a definition.
     */
    public function resolve(Container $container);
}
