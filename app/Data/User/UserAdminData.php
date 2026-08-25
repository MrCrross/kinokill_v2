<?php

namespace App\Data\User;

use App\Data\Role\RoleData;
use App\Models\User;
use Spatie\LaravelData\Data;

class UserAdminData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public ?RoleData $role,
    ) {}

    public static function fromModel(User $user): self
    {
        $role = $user->roles->first();

        return new self(
            id: $user->id,
            name: $user->name,
            email: $user->email,
            role: $role ? RoleData::fromModel($role) : null,
        );
    }
}
