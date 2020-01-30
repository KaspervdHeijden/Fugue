<?php

declare(strict_types=1);

namespace Fugue\Permission;

final class User
{
    /** @var string */
    private $passwordHash;

    /** @var string */
    private $login;

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }
}
