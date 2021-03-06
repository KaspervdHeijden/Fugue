<?php

declare(strict_types=1);

namespace Fugue\Core\ClassLoader;

interface ClassLoaderInterface
{
    public function loadClass(string $className): void;

    public function exists(string $className, bool $autoload): bool;
}
