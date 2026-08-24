<?php

namespace App\Services;

use App\Exceptions\CannotChangeOwnRoleException;
use App\Models\User;

class UserRoleService
{
    /**
     * @throws CannotChangeOwnRoleException
     */
    public function assignRole(User $actor, User $target, string $role): User
    {
        if ($actor->is($target) && $role !== 'admin') {
            throw CannotChangeOwnRoleException::create();
        }

        $target->syncRoles([$role]);

        return $target->fresh();
    }
}
