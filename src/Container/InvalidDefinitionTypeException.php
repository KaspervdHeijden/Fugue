<?php

declare(strict_types=1);

namespace Fugue\Container;

use Fugue\Core\Exception\FugueException;

use function sprintf;

final class InvalidDefinitionTypeException extends FugueException
{
    public static function forDefinitionName(string $name): self
    {
        return new InvalidDefinitionTypeException(sprintf(
            'Unexpected type encountered for definition "%s"',
            $name
        ));
    }
}
