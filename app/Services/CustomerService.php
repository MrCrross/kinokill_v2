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

    /**
     * Resolve the customer profile for an anonymous checkout: reuse an
     * existing customer with this email (guest or registered) so repeat
     * guest bookings and future account registration/login can find their
     * history, or create a fresh guest profile (no user_id) otherwise.
     */
    public function resolveForGuestCheckout(string $name, string $email, ?string $phone): Customer
    {
        $customer = Customer::where('email', $email)->first();

        if ($customer) {
            return $customer;
        }

        return Customer::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
        ]);
    }

    /**
     * Resolve the customer profile for an authenticated user, creating one
     * lazily if it is somehow missing (e.g. an account created outside the
     * normal registration flow).
     */
    public function forAuthenticatedUser(User $user): Customer
    {
        return $user->customer ?? $this->linkOrCreateForUser($user, $user->name, $user->email);
    }
}
