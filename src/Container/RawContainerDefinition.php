<?php

declare(strict_types=1);

namespace Fugue\Container;

final class RawContainerDefinition extends ContainerDefinition
{
    public function resolve(Container $container)
    {
        return $this->definition;
    }
}
