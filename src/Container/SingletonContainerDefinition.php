<?php

declare(strict_types=1);

namespace Fugue\Container;

final class SingletonContainerDefinition extends ContainerDefinition
{
    private bool $resolved = false;
    /** @var mixed */
    private $resolvedValue;

    protected function isValidDefinition($definition): bool
    {
        return is_callable($definition);
    }

    public function resolve(Container $container)
    {
        if (! $this->resolved) {
            $this->resolvedValue = $this->getDefinition()($container);
            $this->resolved      = true;
        }

        return $this->resolvedValue;
    }
}
