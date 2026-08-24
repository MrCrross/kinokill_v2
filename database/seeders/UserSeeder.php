<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Demo accounts for local development/defense (all use password
     * "password"). Every account gets a linked Customer profile so its
     * booking history works the same way a real registration would.
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'Admin', 'email' => 'admin@kinokill.test', 'role' => 'admin'],
            ['name' => 'Manager', 'email' => 'manager@kinokill.test', 'role' => 'manager'],
            ['name' => 'Test User', 'email' => 'test@example.com', 'role' => 'customer'],
        ];

        foreach ($accounts as $account) {
            $user = User::factory()->create([
                'name' => $account['name'],
                'email' => $account['email'],
            ]);
            $user->assignRole($account['role']);

            Customer::factory()->create([
                'name' => $user->name,
                'email' => $user->email,
                'user_id' => $user->id,
            ]);
        }
    }
}
