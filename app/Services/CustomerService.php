<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;

class CustomerService
{
    /**
     * Link an existing guest customer profile (created during an anonymous
     * checkout) to a newly registered/authenticated user, or create a fresh
     * customer profile if no guest booking history exists for this email.
     */
    public function linkOrCreateForUser(User $user, string $name, string $email): Customer
    {
        $guest = Customer::whereNull('user_id')->where('email', $email)->first();

        if ($guest) {
            $guest->update(['user_id' => $user->id]);

            return $guest->fresh();
        }

        return Customer::create([
            'name' => $name,
            'email' => $email,
            'user_id' => $user->id,
        ]);
    }
}
