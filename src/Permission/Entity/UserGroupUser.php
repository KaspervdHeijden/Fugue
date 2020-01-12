<?php

declare(strict_types=1);

namespace Fugue\Permission;

final class UserGroupUser
{
    /** @var UserGroup|null */
    private $userGroup;

    /** @var User|null */
    private $user;

    public function setUserGroup(?UserGroup $userGroup): void
    {
        $this->userGroup = $userGroup;
    }

    public function getUserGroup(): ?UserGroup
    {
        return $this->userGroup;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }
}
