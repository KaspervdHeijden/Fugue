<?php

declare(strict_types=1);

namespace Fugue\Core\ClassLoader;

use function str_replace;
use function is_readable;
use function is_file;

final class DefaultClassLoader implements ClassLoaderInterface
{
    /** @var string */
    private $rootNamespace;

    /** @var string */
    private $rootDir;

    public function __construct(
        string $rootDir,
        string $rootNamespace
    ) {
        $this->rootNamespace = $rootNamespace;
        $this->rootDir       = $rootDir;
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
            (static function (string $fileName): void {
                /** @noinspection PhpIncludeInspection */
                require_once $fileName;
            })($fileName);
        }
    }
}
