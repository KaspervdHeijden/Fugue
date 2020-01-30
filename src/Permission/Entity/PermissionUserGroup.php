<?php

declare(strict_types=1);

namespace Fugue\Permission;

final class PermissionUserGroup
{
    /** @var Permission|null */
    private $permission;

    /** @var UserGroup|null */
    private $userGroup;

    public function setPermission(?Permission $permission): void
    {
        $this->permission = $permission;
    }

    public function getPermission(): ?Permission
    {
        return $this->permission;
    }

    public function setUserGroup(?UserGroup $userGroup): void
    {
        $this->userGroup = $userGroup;
    }

    public function getUserGroup(): ?UserGroup
    {
        return $this->userGroup;
    }
}
