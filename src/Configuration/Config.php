<?php

declare(strict_types=1);

namespace Fugue\Configuration;

use InvalidArgumentException;

use function mb_strtolower;
use function is_scalar;
use function is_array;
use function explode;
use function count;
use function trim;

/**
 * The Configuration class holds all configuration data.
 */
final class Config
{
    /**
     * @var string Separator to use between branches.
     */
    private const NAME_SEPARATOR = '.';

    /**
     * @var array Holds the data.
     */
    private $config;

    /**
     * Config constructor.
     *
     * @param array[]|Config[] $configs
     * @noinspection PhpDocSignatureInspection
     */
    public function __construct(array ...$configs)
    {
        $config = [];
        foreach ($configs as $configItem) {
            $this->merge(
                $configItem instanceof Config ? $configItem->config : $configItem,
                $config
            );
        }

        $this->config = $config;
    }

    /**
     * Gets the requested configuration by name.
     *
     * @param string $name The name of the configuration to load.
     * @return mixed       The value at the specified path.
     */
    private function getByName(string $name)
    {
        $path = explode('.', mb_strtolower(trim($name, self::NAME_SEPARATOR)));
        if (count($path) === 0) {
            throw new InvalidArgumentException('Name cannot be empty.');
        }

        $data = $this->config;
        foreach ($path as $key) {
            if (! isset($data[$key])) {
                throw new InvalidArgumentException(
                    "Invalid name {$name}. Please check entry {$key}."
                );
            }

            $data = $data[$key];
        }

        return $data;
    }

    /**
     * Gets a scalar value by name.
     *
     * @param string $name           The path of the configuration to load.
     * @return bool|float|int|string The value for the configuration. Should be a scalar type.
     */
    public function getValue(string $name)
    {
        $data = $this->getByName($name);
        if (! is_scalar($data)) {
            throw new InvalidArgumentException(
                "Cannot load non-scalar configuration {$name}."
            );
        }

        return $data;
    }

    /**
     * Copies values from one array to another, recursively.
     *
     * @param array $newConfig The key/values of the overrides.
     * @param array $config    The source array.
     */
    private function merge(array $newConfig, array &$config): void
    {
        foreach ($newConfig as $key => $value) {
            if (is_array($value)) {
                $config[$key] = $config[$key] ?? [];
                $this->merge($value, $config[$key]);
            } else {
                $config[$key] = $value;
            }
        }
    }

    /**
     * Loads a branch from this configuration.
     *
     * @param string $path   The path of the branch to load.
     * @return SettingBranch The branch.
     */
    public function getBranch(string $path): SettingBranch
    {
        $data = $this->getByName($path);
        if (! is_array($data)) {
            throw new InvalidArgumentException(
                "Cannot load branch {$path} because it is not an array."
            );
        }

        return new SettingBranch($path, $data);
    }
}
