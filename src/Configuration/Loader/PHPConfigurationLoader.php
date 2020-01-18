<?php

declare(strict_types=1);

namespace Fugue\Configuration\Loader;

use LogicException;

use function is_readable;
use function is_file;

final class PHPConfigurationLoader implements ConfigurationLoaderInterface
{
    private const FILENAME_SUFFIX = '.inc.php';

    /** @var string */
    private $directory;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    private function getFullPathForIdentifier(string $identifier): string
    {
        return "{$this->directory}/{$identifier}" . self::FILENAME_SUFFIX;
    }

    public function supports(string $identifier): bool
    {
        $fileName = $this->getFullPathForIdentifier($identifier);
        return is_file($fileName) && is_readable($fileName);
    }

    public function load(string $identifier)
    {
        if (! $this->supports($identifier)) {
            throw new LogicException(static::class . " does not support '{$identifier}'");
        }

        /** @noinspection PhpIncludeInspection */
        return require_once $this->getFullPathForIdentifier($identifier);
    }
}
