<?php

namespace App\Data\Permission;

use App\Models\Permission;
use Spatie\LaravelData\Data;

class PermissionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public ?string $label,
        public ?string $description,
    ) {}

    public static function fromModel(Permission $permission): self
    {
        return new self(
            id: $permission->id,
            name: $permission->name,
            label: $permission->label,
            description: $permission->description,
        );
    }
}
