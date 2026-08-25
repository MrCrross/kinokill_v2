<?php

namespace App\Data\Role;

use App\Data\Permission\PermissionData;
use App\Models\Role;
use Spatie\LaravelData\Data;

class RoleData extends Data
{
    /**
     * @param  array<int, PermissionData>  $permissions
     */
    public function __construct(
        public int $id,
        public string $name,
        public ?string $label,
        public ?string $description,
        public array $permissions,
    ) {}

    public static function fromModel(Role $role): self
    {
        return new self(
            id: $role->id,
            name: $role->name,
            label: $role->label,
            description: $role->description,
            permissions: $role->permissions
                ->map(fn ($permission) => PermissionData::fromModel($permission))
                ->values()
                ->all(),
        );
    }
}
