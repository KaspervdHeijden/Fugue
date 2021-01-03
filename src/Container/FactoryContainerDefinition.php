<?php

declare(strict_types=1);

namespace Fugue\Container;

use function is_callable;

final class FactoryContainerDefinition extends ContainerDefinition
{
    protected function isValidDefinition($definition): bool
    {
        return is_callable($definition);
    }

    public function resolve(Container $container)
    {
        return $this->getDefinition()($container);
    }
}
