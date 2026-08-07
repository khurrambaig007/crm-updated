<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_the_registration_page(): void
    {
        $this->get(route('register'))->assertStatus(200);
    }

    public function test_users_can_register(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
        ]);
    }

    public function test_registration_requires_unique_email(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_registration_requires_password_confirmation(): void
    {
        $this->post(route('register.store'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }
}
