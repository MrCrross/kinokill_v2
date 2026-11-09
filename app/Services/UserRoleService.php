<?php

namespace App\Services;

use App\Data\Pagination\PaginatedData;
use App\Data\User\StoreUserData;
use App\Data\User\UserAdminData;
use App\Exceptions\CannotRevokeOwnAccessException;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

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

    /**
     * Create a staff/customer account without a password and email the
     * standard "reset password" link so the invitee sets their own. The
     * email is trusted at this point (the admin typed it, and completing
     * the reset flow proves inbox ownership), so it's marked verified
     * immediately instead of also running the separate email-verification
     * flow on top.
     */
    public function inviteUser(StoreUserData $data): User
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make(Str::random(40)),
        ]);

        // email_verified_at is intentionally not mass-assignable (User's
        // Fillable list), so it's set here via forceFill rather than being
        // passed to create() above.
        $user->forceFill(['email_verified_at' => now()])->save();

        if ($data->roleId) {
            $user->syncRoles([Role::findOrFail($data->roleId)]);
        }

        Password::sendResetLink(['email' => $user->email]);

        return $user;
    }

    public function paginateUsers(?string $search, ?int $roleId): PaginatedData
    {
        $paginator = User::query()
            ->with('roles.permissions')
            ->when($search, fn ($query, $search) => $query->where(
                fn ($query) => $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
            ))
            ->when($roleId, fn ($query, $roleId) => $query->whereHas(
                'roles',
                fn ($query) => $query->where('roles.id', $roleId)
            ))
            ->orderBy('name')
            ->paginate(15);

        return PaginatedData::fromPaginator($paginator, fn (User $user) => UserAdminData::fromModel($user));
    }
}
