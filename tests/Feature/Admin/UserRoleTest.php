<?php

namespace Tests\Feature\Admin;

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

        $this->actingAs($admin)->put("/admin/users/{$customer->id}/role", ['role' => 'manager'])
            ->assertRedirect('/admin/users');

        $this->assertTrue($customer->fresh()->hasRole('manager'));
        $this->assertFalse($customer->fresh()->hasRole('customer'));
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put("/admin/users/{$admin->id}/role", ['role' => 'customer']);

        $this->assertTrue($admin->fresh()->hasRole('admin'));
    }

    public function test_manager_cannot_manage_user_roles(): void
    {
        $manager = User::factory()->manager()->create();
        $customer = User::factory()->customer()->create();

        $this->actingAs($manager)->get('/admin/users')->assertForbidden();
        $this->actingAs($manager)->put("/admin/users/{$customer->id}/role", ['role' => 'admin'])->assertForbidden();
    }
}
