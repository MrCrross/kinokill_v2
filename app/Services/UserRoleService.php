<?php

namespace App\Services;

use App\Exceptions\CannotRevokeOwnAccessException;
use App\Models\Role;
use App\Models\User;

class UserRoleService
{
    /**
     * @throws CannotRevokeOwnAccessException
     */
    public function assignRole(User $actor, User $target, Role $role): User
    {
        if ($actor->is($target) && ! $role->hasPermissionTo('users.manage-roles')) {
            throw CannotRevokeOwnAccessException::create();
        }

        $target->syncRoles([$role]);

        return $target->fresh();
    }
}
