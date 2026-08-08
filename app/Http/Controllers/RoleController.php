<?php

namespace App\Http\Controllers;

use App\DataTables\RolesDataTable;
use App\Http\Requests\Role\StoreRoleRequest;
use App\Http\Requests\Role\UpdateRoleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request, RolesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('roles.index');
    }

    public function create(): View
    {
        return view('roles.create');
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        if (strtolower($request->string('name')->value()) === strtolower(config('system.super_admin_role'))) {
            return redirect()->route('roles.create')->with('error', 'The '.config('system.super_admin_role').' role name is reserved.');
        }

        $role = Role::create(['name' => $request->string('name')]);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('status', 'Role created successfully.');
    }

    public function edit(Role $role): View
    {
        return view('roles.edit', [
            'role' => $role,
            'rolePermissions' => $role->permissions->pluck('name')->all(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        if ($role->name === config('system.super_admin_role')) {
            return redirect()->route('roles.index')->with('error', 'The Super Admin role cannot be modified.');
        }

        $role->update(['name' => $request->string('name')]);
        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')->with('status', 'Role updated successfully.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        if ($role->name === config('system.super_admin_role')) {
            return redirect()->route('roles.index')->with('error', 'The Super Admin role cannot be deleted.');
        }

        $role->delete();

        return redirect()->route('roles.index')->with('status', 'Role deleted successfully.');
    }
}
