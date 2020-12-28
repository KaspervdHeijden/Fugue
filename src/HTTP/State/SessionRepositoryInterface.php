<?php

declare(strict_types=1);

namespace Fugue\HTTP\State;

interface SessionRepositoryInterface
{
    public function getByName(string $userIdentifier, string $name);

    public function persist(string $userIdentifier, string $name, $value): void;

    public function exists(string $userIdentifier, string $name): bool;

    public function delete(string $userIdentifier, string $name): void;

    public function clear(string $userIdentifier): void;
}
