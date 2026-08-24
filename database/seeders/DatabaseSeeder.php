<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * WithoutModelEvents is intentionally NOT used here: Model::withoutEvents()
     * suppresses events for every model for the whole run() call, including
     * nested seeders — which breaks spatie/laravel-permission's cache
     * invalidation (it listens on Permission/Role model events).
     */
    public function run(): void
    {
        $this->call(RoleAndPermissionSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@kinokill.test',
        ]);
        $admin->assignRole('admin');

        $manager = User::factory()->create([
            'name' => 'Manager',
            'email' => 'manager@kinokill.test',
        ]);
        $manager->assignRole('manager');

        $customer = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        $customer->assignRole('customer');
        Customer::factory()->create([
            'name' => $customer->name,
            'email' => $customer->email,
            'user_id' => $customer->id,
        ]);
    }
}
