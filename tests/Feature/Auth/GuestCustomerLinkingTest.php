<?php

namespace Tests\Feature\Auth;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestCustomerLinkingTest extends TestCase
{
    use RefreshDatabase;

    public function test_registering_with_an_existing_guest_customer_email_links_the_account(): void
    {
        $guest = Customer::factory()->create([
            'name' => 'Guest Buyer',
            'email' => 'guest@example.com',
        ]);

        $this->assertNull($guest->user_id);

        $response = $this->post('/register', [
            'name' => 'Guest Buyer',
            'email' => 'guest@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'guest@example.com')->firstOrFail();

        $this->assertTrue($user->hasRole('customer'));
        $this->assertSame(1, Customer::where('email', 'guest@example.com')->count());
        $this->assertSame($guest->id, $user->customer->id);
        $this->assertSame($user->id, $guest->fresh()->user_id);
    }

    public function test_registering_with_a_new_email_creates_a_linked_customer_profile(): void
    {
        $this->post('/register', [
            'name' => 'Brand New User',
            'email' => 'new@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'new@example.com')->firstOrFail();

        $this->assertNotNull($user->customer);
        $this->assertSame('new@example.com', $user->customer->email);
    }
}
