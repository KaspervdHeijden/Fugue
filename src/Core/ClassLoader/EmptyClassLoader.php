<?php

declare(strict_types=1);

namespace Fugue\Core\ClassLoader;

final class EmptyClassLoader implements ClassLoaderInterface
{
    public function loadClass(string $className): void
    {
    }
}
