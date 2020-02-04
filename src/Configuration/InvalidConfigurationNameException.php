<?php

declare(strict_types=1);

namespace Fugue\Configuration;

use Fugue\Core\Exception\FugueException;

final class InvalidConfigurationNameException extends FugueException
{
    public static function forEmptyName(): self
    {
        return new static('Name cannot be empty.');
    }

    public static function forNonExistentName(string $name, string $nonExistentKey): self
    {
        return new static(
            "Invalid name {$name}. Please check entry {$nonExistentKey}."
        );
    }

    public static function forNonScalarType(string $name): self
    {
        return new static("Cannot load non-scalar configuration {$name}.");
    }

    public static function forNonBranchType(string $name): self
    {
        return new static("Cannot load branch {$name} because it is not an array.");
    }
}
