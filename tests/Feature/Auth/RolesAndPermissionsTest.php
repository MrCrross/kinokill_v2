<?php

namespace Tests\Feature\Auth;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RolesAndPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_and_permissions_are_seeded_with_russian_labels_and_descriptions(): void
    {
        Role::all()->each(function (Role $role) {
            $this->assertNotEmpty($role->label, "Role [{$role->name}] is missing a label.");
            $this->assertNotEmpty($role->description, "Role [{$role->name}] is missing a description.");
        });

        Permission::all()->each(function (Permission $permission) {
            $this->assertNotEmpty($permission->label, "Permission [{$permission->name}] is missing a label.");
            $this->assertNotEmpty($permission->description, "Permission [{$permission->name}] is missing a description.");
        });
    }

    public function test_admin_role_has_full_management_permissions(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->can('films.manage'));
        $this->assertTrue($admin->can('sessions.manage'));
        $this->assertTrue($admin->can('halls.manage'));
        $this->assertTrue($admin->can('bookings.manage-any'));
        $this->assertTrue($admin->can('users.manage-roles'));
    }

    public function test_manager_role_can_manage_sessions_and_bookings_but_not_films_or_users(): void
    {
        $manager = User::factory()->manager()->create();

        $this->assertTrue($manager->can('sessions.manage'));
        $this->assertTrue($manager->can('bookings.manage-any'));
        $this->assertFalse($manager->can('films.manage'));
        $this->assertFalse($manager->can('halls.manage'));
        $this->assertFalse($manager->can('users.manage-roles'));
    }

    public function test_customer_role_has_no_management_permissions(): void
    {
        $customer = User::factory()->customer()->create();

        $this->assertTrue($customer->hasRole('customer'));
        $this->assertFalse($customer->can('films.manage'));
        $this->assertFalse($customer->can('sessions.manage'));
        $this->assertFalse($customer->can('bookings.manage-any'));
    }

    public function test_plain_user_without_assigned_role_has_no_permissions(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('films.manage'));
        $this->assertFalse($user->can('bookings.manage-any'));
    }
}
