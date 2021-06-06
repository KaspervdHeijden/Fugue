<?php

declare(strict_types=1);

namespace Fugue\Container;

interface ContainerDefinitionInterface
{
    public function resolve(Container $container): mixed;
}
