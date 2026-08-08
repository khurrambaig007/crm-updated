<x-app-layout :title="'Permissions'">
    @php
        $actionLabels = [
            'view' => 'View',
            'add' => 'Add',
            'edit' => 'Edit',
            'delete' => 'Delete',
        ];
        $allActions = collect(config('system.screens'))->flatMap(fn ($s) => $s['permissions'])->unique()->values()->all();
    @endphp

    <div class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Permissions</h1>
                <p class="mt-1 text-sm text-topbar-muted">Control granular access for every screen in your CRM.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                {{ session('error') }}
            </div>
        @endif

        <div class="rounded-2xl bg-card-bg shadow-sm ring-1 ring-card-border">
            <div class="flex flex-col gap-4 border-b border-gray-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold text-topbar-text">Role permission matrix</h2>
                    <p class="mt-0.5 text-sm text-topbar-muted">Select a role to review and edit its permissions.</p>
                </div>
                <div class="w-full sm:w-64">
                    <select id="role-selector" class="block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500">
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ $role->name === $superAdminRole ? 'data-super-admin' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <form id="permissions-form" method="POST" action="{{ route('permissions.update', ['role' => '__ROLE__']) }}">
                @csrf
                @method('PATCH')

                <div id="super-admin-notice" class="hidden px-6 py-4">
                    <div class="rounded-xl bg-primary-50 px-4 py-3 text-sm font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">
                        This is the Super Admin role — it always holds every permission and cannot be modified.
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-100 text-left">
                                <th class="px-6 py-4 text-xs font-semibold uppercase tracking-wider text-topbar-muted">Screen</th>
                                @foreach ($allActions as $action)
                                    <th class="px-4 py-4 text-center text-xs font-semibold uppercase tracking-wider text-topbar-muted">
                                        <label class="inline-flex cursor-pointer items-center gap-2">
                                            {{ $actionLabels[$action] ?? ucfirst($action) }}
                                            <input type="checkbox" class="column-toggle h-3.5 w-3.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500" data-action="{{ $action }}">
                                        </label>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (config('system.screens') as $screenKey => $screen)
                                <tr class="border-b border-gray-50 transition-colors hover:bg-gray-50/50">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-topbar-text">{{ $screen['label'] }}</div>
                                        <div class="text-xs text-topbar-muted">{{ $screenKey }} module</div>
                                    </td>
                                    @foreach ($allActions as $action)
                                        @php
                                            $permissionName = $screenKey.'.'.$action;
                                            $supported = in_array($action, $screen['permissions']);
                                        @endphp
                                        <td class="px-4 py-4 text-center">
                                            @if ($supported)
                                                <input type="checkbox" name="permissions[]" value="{{ $permissionName }}" class="permission-checkbox h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" data-action="{{ $action }}">
                                            @else
                                                <span class="text-gray-300">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between gap-4 border-t border-gray-100 px-6 py-4">
                    <p class="text-sm text-topbar-muted">Unchecked screens remain locked for this role.</p>
                    <button type="submit" id="save-permissions" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-900 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="M20 6 9 17l-5-5" />
                        </svg>
                        Save permissions
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selector = document.getElementById('role-selector');
            const form = document.getElementById('permissions-form');
            const notice = document.getElementById('super-admin-notice');
            const saveBtn = document.getElementById('save-permissions');
            const checkboxes = Array.from(document.querySelectorAll('.permission-checkbox'));
            const columnToggles = Array.from(document.querySelectorAll('.column-toggle'));

            const permissionsByRole = @json($roles->mapWithKeys(fn ($role) => [$role->id => $role->permissions->pluck('name')->all()]));

            function isSuperAdmin(id) {
                const opt = selector.querySelector('option[value="'+id+'"]');
                return opt ? opt.hasAttribute('data-super-admin') : false;
            }

            function applyRole(id) {
                const superAdmin = isSuperAdmin(id);
                notice.classList.toggle('hidden', !superAdmin);
                saveBtn.disabled = superAdmin;

                const perms = new Set(permissionsByRole[id] ?? []);
                checkboxes.forEach(cb => { cb.checked = perms.has(cb.value); });

                form.action = form.action.replace('__ROLE__', id);
            }

            selector.addEventListener('change', () => applyRole(selector.value));
            applyRole(selector.value);

            columnToggles.forEach(toggle => {
                toggle.addEventListener('change', function () {
                    const action = this.dataset.action;
                    checkboxes.forEach(cb => {
                        if (cb.dataset.action === action) cb.checked = toggle.checked;
                    });
                });
            });
        });
    </script>
</x-app-layout>
