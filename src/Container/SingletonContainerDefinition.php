<?php

declare(strict_types=1);

namespace Fugue\Container;

final class SingletonContainerDefinition extends ContainerDefinition
{
    private bool $resolved = false;

    protected function isValidDefinition($definition): bool
    {
        return is_callable($definition);
    }

    public function resolve(Container $container)
    {
        if (! $this->resolved) {
            $this->definition = ($this->definition)($container);
            $this->resolved   = true;
        }

        return $this->definition;
    }
}
