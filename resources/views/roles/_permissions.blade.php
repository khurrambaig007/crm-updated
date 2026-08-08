@php
    $checkedPermissions = $checkedPermissions ?? [];
    $permissionLabels = [
        'view' => 'View',
        'add' => 'Add',
        'edit' => 'Edit',
        'delete' => 'Delete',
    ];
@endphp

<div class="rounded-xl ring-1 ring-inset ring-card-border overflow-hidden">
    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-5 py-4">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h3 class="text-sm font-semibold text-topbar-text">Permissions</h3>
                <p class="mt-0.5 text-xs text-topbar-muted">Choose what this role is allowed to do.</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-medium text-topbar-muted hidden sm:inline">Select all</span>
                <label class="relative inline-flex cursor-pointer items-center">
                    <input type="checkbox" id="select-all-permissions" class="peer sr-only">
                    <div class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-0.5 after:top-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all peer-checked:bg-primary-600 peer-checked:after:translate-x-5"></div>
                </label>
            </div>
        </div>
    </div>

    <div class="divide-y divide-gray-100">
        @foreach (config('system.screens') as $screenKey => $screen)
            <div class="flex flex-col gap-3 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="min-w-0">
                    <div class="text-sm font-medium text-topbar-text">{{ $screen['label'] }}</div>
                    <div class="text-xs text-topbar-muted">{{ $screenKey }} module</div>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($screen['permissions'] as $action)
                        @php
                            $permissionName = $screenKey.'.'.$action;
                            $isChecked = in_array($permissionName, $checkedPermissions);
                        @endphp
                        <label class="inline-flex cursor-pointer items-center gap-1.5 rounded-lg border px-3 py-1.5 text-xs font-medium transition-colors {{ $isChecked ? 'border-primary-500 bg-primary-50 text-primary-700' : 'border-gray-200 text-topbar-muted hover:border-gray-300' }}">
                            <input type="checkbox" name="permissions[]" value="{{ $permissionName }}" class="permission-checkbox h-3.5 w-3.5 rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ $isChecked ? 'checked' : '' }}>
                            {{ $permissionLabels[$action] ?? ucfirst($action) }}
                        </label>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all-permissions');
        if (!selectAll) return;

        const checkboxes = document.querySelectorAll('.permission-checkbox');
        const allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(cb => cb.checked);
        selectAll.checked = allChecked;

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => { cb.checked = selectAll.checked; });
        });

        checkboxes.forEach(cb => {
            cb.addEventListener('change', function () {
                selectAll.checked = Array.from(checkboxes).every(c => c.checked);
            });
        });
    });
</script>
