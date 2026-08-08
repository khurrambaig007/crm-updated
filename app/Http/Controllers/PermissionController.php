<?php

namespace App\Http\Controllers;

use App\Http\Requests\Permission\UpdatePermissionsRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index(): View
    {
        return view('permissions.index', [
            'screens' => config('system.screens'),
            'roles' => Role::with('permissions')->get(),
            'superAdminRole' => config('system.super_admin_role'),
        ]);
    }

    public function update(UpdatePermissionsRequest $request, Role $role): RedirectResponse
    {
        if ($role->name === config('system.super_admin_role')) {
            return redirect()->route('permissions.index')->with('error', 'The Super Admin role always holds every permission.');
        }

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('permissions.index')->with('status', 'Permissions updated for '.$role->name.'.');
    }
}
