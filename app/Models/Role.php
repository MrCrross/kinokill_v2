<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

/**
 * A named bag of permissions that can be assigned to a user. The role's
 * `name` is the machine slug spatie matches against; `label`/`description`
 * are the human-readable Russian text shown in the admin UI so that roles
 * (and, on Permission, individual rights) are self-documenting instead of
 * being hardcoded strings scattered through business logic.
 */
class Role extends SpatieRole
{
    protected $fillable = [
        'name',
        'guard_name',
        'label',
        'description',
    ];
}
