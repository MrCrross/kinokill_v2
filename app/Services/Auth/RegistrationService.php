<?php

namespace App\Services\Auth;

use App\Data\Auth\RegisterUserData;
use App\Models\User;
use App\Services\CustomerService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;

class RegistrationService
{
    public function __construct(
        private readonly CustomerService $customers,
    ) {}

    public function register(RegisterUserData $data): User
    {
        $user = User::create([
            'name' => $data->name,
            'email' => $data->email,
            'password' => Hash::make($data->password),
        ]);

        $user->assignRole(config('roles.default_customer_role'));

        $this->customers->linkOrCreateForUser($user, $data->name, $data->email);

        event(new Registered($user));

        return $user;
    }
}
