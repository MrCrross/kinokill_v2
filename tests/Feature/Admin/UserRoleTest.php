<?php

namespace Tests\Feature\Admin;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_a_paginated_user_list(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->customer()->create();

        $response = $this->actingAs($admin)->get('/admin/users');

        // 3 customers + the acting admin themselves.
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Admin/Users/Index')
                ->has('users.data', 4)
                ->where('users.total', 4)
        );
    }

    public function test_admin_can_search_users_by_name_or_email(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->customer()->create(['name' => 'Иван Петров', 'email' => 'ivan@example.com']);
        User::factory()->customer()->create(['name' => 'Анна Смирнова', 'email' => 'anna@example.com']);

        $response = $this->actingAs($admin)->get('/admin/users?search=Иван');

        $response->assertInertia(
            fn (Assert $page) => $page
                ->has('users.data', 1)
                ->where('users.data.0.name', 'Иван Петров')
        );
    }

    public function test_admin_can_filter_users_by_role(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->manager()->create();
        User::factory()->customer()->create();
        $managerRole = Role::where('name', 'manager')->firstOrFail();

        $response = $this->actingAs($admin)->get("/admin/users?role_id={$managerRole->id}");

        $response->assertInertia(fn (Assert $page) => $page->has('users.data', 1));
    }

    public function test_admin_can_invite_a_new_user_with_a_role(): void
    {
        Notification::fake();
        $admin = User::factory()->admin()->create();
        $managerRole = Role::where('name', 'manager')->firstOrFail();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Новый менеджер',
            'email' => 'new-manager@example.com',
            'role_id' => $managerRole->id,
        ]);

        $response->assertRedirect('/admin/users');
        $user = User::where('email', 'new-manager@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('manager'));
        $this->assertNotNull($user->email_verified_at);
        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_inviting_a_user_with_an_existing_email_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['email' => 'taken@example.com']);

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Кто-то',
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors('email');
    }

    public function test_manager_cannot_invite_users(): void
    {
        $manager = User::factory()->manager()->create();

        $this->actingAs($manager)->post('/admin/users', [
            'name' => 'Кто-то',
            'email' => 'someone@example.com',
        ])->assertForbidden();
    }

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
