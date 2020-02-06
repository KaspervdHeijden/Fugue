<?php

declare(strict_types=1);

namespace Fugue\Core\ClassLoader;

interface ClassLoaderInterface
{
    /**
     * Attempts to load a class.
     *
     * @param string $className The fully qualified name of the class to load.
     */
    public function loadClass(string $className): void;

    /**
     * Registers this class loader.
     */
    public function register(): void;
}
