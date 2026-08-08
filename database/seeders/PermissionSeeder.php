<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = collect(config('system.screens'))
            ->flatMap(fn (array $screen, string $screenKey) => collect($screen['permissions'])
                ->map(fn (string $action) => $screenKey.'.'.$action))
            ->unique();

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        $superAdmin = Role::firstOrCreate(['name' => config('system.super_admin_role')]);
        $superAdmin->syncPermissions($permissions);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
