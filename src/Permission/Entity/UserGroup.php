<?php

declare(strict_types=1);

namespace Fugue\Permission;

final class UserGroup
{
    /** @var string */
    private $name = '';

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
