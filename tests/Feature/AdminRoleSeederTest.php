<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\PermissionSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminRoleSeederTest extends BaseTestCase
{
    use RefreshDatabase;

    public function test_admin_role_is_seeded_with_all_permissions(): void
    {
        $this->seed();

        $adminRole = Role::where('name', config('system.admin_role'))->first();

        $this->assertNotNull($adminRole, 'Admin role should be seeded');
        $this->assertSame(Permission::count(), $adminRole->permissions()->count());

        // Assert against the configured set rather than a hardcoded number, so adding
        // a new screen does not require editing this test.
        $this->assertEqualsCanonicalizing(
            app(PermissionSyncService::class)->configuredPermissions(),
            $adminRole->permissions()->pluck('name')->all(),
        );
    }

    public function test_test_user_is_assigned_the_admin_role(): void
    {
        $this->seed();

        $testUser = User::where('email', 'test@example.com')->first();

        $this->assertNotNull($testUser, 'test@example.com user should be seeded');
        $this->assertSame('Test User', $testUser->name);
        $this->assertTrue($testUser->hasRole(config('system.admin_role')));
    }

    public function test_super_admin_role_and_user_remain_intact(): void
    {
        $this->seed();

        $this->assertTrue(Role::where('name', config('system.super_admin_role'))->exists());
        $this->assertTrue(User::where('email', 'superadmin@example.com')->first()->hasRole(config('system.super_admin_role')));
    }
}
