<?php

declare(strict_types=1);

namespace Fugue\Container;

use function is_callable;

final class FactoryContainerDefinition extends ContainerDefinition
{
    protected function isValidDefinition(mixed $definition): bool
    {
        return is_callable($definition);
    }

    public function resolve(Container $container): mixed
    {
        return $this->getDefinition()($container);
    }
}
