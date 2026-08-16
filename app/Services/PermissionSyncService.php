<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSyncService
{
    /**
     * Build the full list of permission names from the system screens config.
     *
     * @return array<int, string>
     */
    public function configuredPermissions(): array
    {
        return collect(config('system.screens'))
            ->flatMap(fn (array $screen, string $screenKey) => collect($screen['permissions'])
                ->map(fn (string $action) => $screenKey.'.'.$action))
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Determine whether any configured permission is missing from the database.
     */
    public function hasMissingPermissions(): bool
    {
        $existing = Permission::pluck('name')->all();

        return collect($this->configuredPermissions())
            ->diff($existing)
            ->isNotEmpty();
    }

    /**
     * Create any missing permissions and assign all of them to the super admin role.
     *
     * @return array{created: int, assigned: int}
     */
    public function sync(): array
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = $this->configuredPermissions();
        $created = 0;

        foreach ($permissions as $permissionName) {
            if (Permission::firstOrCreate(['name' => $permissionName])->wasRecentlyCreated) {
                $created++;
            }
        }

        $superAdmin = Role::firstOrCreate(['name' => config('system.super_admin_role')]);
        $superAdmin->syncPermissions($permissions);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        return [
            'created' => $created,
            'assigned' => count($permissions),
        ];
    }
}
