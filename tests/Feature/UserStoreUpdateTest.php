<?php

namespace Tests\Feature;

use App\Http\Middleware\PermissionMiddleware;
use App\Models\User;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

class UserStoreUpdateTest extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware([Authenticate::class, PermissionMiddleware::class]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'theme' => 'slate-orange',
        ]);
    }

    private function createRole(string $name): Role
    {
        return Role::create(['name' => $name]);
    }

    private function userPayload(): array
    {
        return [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'theme' => 'slate-orange',
        ];
    }

    public function test_store_assigns_the_selected_role(): void
    {
        $this->createRole('booker');

        $this->post(route('users.store'), $this->userPayload() + ['role' => 'booker'])
            ->assertRedirect(route('users.index'));

        $user = User::where('email', 'jane@example.com')->first();

        $this->assertTrue($user->hasRole('booker'));
        $this->assertDatabaseHas('model_has_roles', [
            'model_type' => User::class,
            'model_id' => $user->id,
            'role_id' => Role::where('name', 'booker')->value('id'),
        ]);
    }

    public function test_store_without_a_role_assigns_none(): void
    {
        $this->post(route('users.store'), $this->userPayload())
            ->assertRedirect(route('users.index'));

        $user = User::where('email', 'jane@example.com')->first();

        $this->assertFalse($user->hasAnyRole());
        $this->assertDatabaseCount('model_has_roles', 0);
    }

    public function test_update_replaces_the_role(): void
    {
        $this->createRole('booker');
        $this->createRole('manager');

        $user = User::create([
            'name' => 'Role Holder',
            'email' => 'holder@example.com',
            'password' => bcrypt('password'),
            'theme' => 'slate-teal',
        ]);
        $user->assignRole('booker');

        $this->patch(route('users.update', $user), [
            'name' => 'Role Holder',
            'email' => 'holder@example.com',
            'theme' => 'slate-teal',
            'role' => 'manager',
        ])->assertRedirect(route('users.index'));

        $user->refresh();

        $this->assertTrue($user->hasRole('manager'));
        $this->assertFalse($user->hasRole('booker'));
    }

    public function test_update_without_a_role_clears_all_roles(): void
    {
        $this->createRole('booker');

        $user = User::create([
            'name' => 'Role Holder',
            'email' => 'holder@example.com',
            'password' => bcrypt('password'),
            'theme' => 'slate-teal',
        ]);
        $user->assignRole('booker');

        $this->patch(route('users.update', $user), [
            'name' => 'Role Holder',
            'email' => 'holder@example.com',
            'theme' => 'slate-teal',
        ])->assertRedirect(route('users.index'));

        $user->refresh();

        $this->assertFalse($user->hasAnyRole());
    }
}
