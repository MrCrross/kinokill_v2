<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_promote_a_customer_to_manager(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->customer()->create();
        $manager = Role::where('name', 'manager')->firstOrFail();

        $this->actingAs($admin)->put("/admin/users/{$customer->id}/role", ['role_id' => $manager->id])
            ->assertRedirect('/admin/users');

        $this->assertTrue($customer->fresh()->hasRole('manager'));
        $this->assertFalse($customer->fresh()->hasRole('customer'));
    }

    public function test_admin_cannot_assign_themselves_a_role_without_the_manage_roles_permission(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = Role::where('name', 'customer')->firstOrFail();

        $this->actingAs($admin)->put("/admin/users/{$admin->id}/role", ['role_id' => $customer->id]);

        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    public function test_admin_can_reassign_themselves_a_role_that_still_grants_manage_roles(): void
    {
        $admin = User::factory()->admin()->create();

        $otherAdminRole = Role::create([
            'name' => 'senior-admin',
            'label' => 'Старший администратор',
        ]);
        $otherAdminRole->syncPermissions(['users.manage-roles']);

        $this->actingAs($admin)->put("/admin/users/{$admin->id}/role", ['role_id' => $otherAdminRole->id])
            ->assertRedirect('/admin/users');

        $this->assertTrue($admin->fresh()->hasRole('senior-admin'));
    }

    public function test_manager_cannot_manage_user_roles(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = User::factory()->customer()->create();
        $admin = Role::where('name', 'admin')->firstOrFail();

        $this->actingAs($manager)->get('/admin/users')->assertForbidden();
        $this->actingAs($manager)->put("/admin/users/{$customer->id}/role", ['role_id' => $admin->id])->assertForbidden();
    }
}
