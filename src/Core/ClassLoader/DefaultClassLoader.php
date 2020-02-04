<?php

declare(strict_types=1);

namespace Fugue\Core\ClassLoader;

use function spl_autoload_register;
use function str_replace;
use function is_readable;
use function is_file;

final class DefaultClassLoader implements ClassLoaderInterface
{
    /** @var string */
    private $rootNamespace;

    /** @var bool */
    private $registered;

    /** @var string */
    private $rootDir;

    public function __construct(string $rootDir, string $rootNamespace)
    {
        $this->rootNamespace = $rootNamespace;
        $this->rootDir       = $rootDir;
        $this->registered    = false;
    }

    private function classNameToFileName(string $className): string
    {
        return str_replace(
            ['\\', $this->rootNamespace],
            ['/', $this->rootDir],
            $className
        );
    }

    public function loadClass(string $className): void
    {
        $fileName = $this->classNameToFileName($className);
        if (is_file($fileName) && is_readable($fileName)) {
            /** @noinspection PhpIncludeInspection */
            require_once $fileName;
        }
    }

    public function register(): void
    {
        if ($this->registered) {
            return;
        }

        $this->registered = (bool)spl_autoload_register(
            [$this, 'loadClass'],
            true,
            true
        );
    }
}
