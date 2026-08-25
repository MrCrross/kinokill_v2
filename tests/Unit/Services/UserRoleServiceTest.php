<?php

namespace Tests\Unit\Services;

use App\Exceptions\CannotRevokeOwnAccessException;
use App\Models\Role;
use App\Models\User;
use App\Services\UserRoleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_actor_can_assign_a_role_to_someone_else(): void
    {
        $admin = User::factory()->admin()->create();
        $target = User::factory()->customer()->create();
        $manager = Role::where('name', 'manager')->firstOrFail();

        $updated = app(UserRoleService::class)->assignRole($admin, $target, $manager);

        $this->assertTrue($updated->hasRole('manager'));
    }

    public function test_actor_cannot_assign_themselves_a_role_lacking_the_manage_roles_permission(): void
    {
        $admin = User::factory()->admin()->create();
        $customerRole = Role::where('name', 'customer')->firstOrFail();

        $this->expectException(CannotRevokeOwnAccessException::class);

        app(UserRoleService::class)->assignRole($admin, $admin, $customerRole);
    }

    public function test_actor_can_assign_themselves_any_role_that_still_grants_manage_roles(): void
    {
        $admin = User::factory()->admin()->create();

        $seniorAdmin = Role::create(['name' => 'senior-admin', 'label' => 'Старший администратор']);
        $seniorAdmin->syncPermissions(['users.manage-roles']);

        $updated = app(UserRoleService::class)->assignRole($admin, $admin, $seniorAdmin);

        $this->assertTrue($updated->hasRole('senior-admin'));
    }
}
