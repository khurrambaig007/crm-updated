<x-app-layout :title="'Edit Role'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $submitClasses = 'rounded-lg bg-primary-900 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $isSuperAdmin = $role->name === config('system.super_admin_role');
    @endphp

    <div class="mx-auto max-w-3xl space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.shield', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Edit role</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Update the details and permissions for {{ $role->name }}.</p>
                </div>
            </div>
            <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>
                Back to roles
            </a>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form('PATCH', route('roles.update', $role))->class('space-y-5')->open() !!}

                <div>
                    <label for="name" class="{{ $labelClasses }}">Role name <span class="text-red-500">*</span></label>
                    {!! html()->text('name', $role->name)->class($inputClasses)->required()->attribute('autocomplete', 'off')->attributeIf($isSuperAdmin, 'disabled', '') !!}
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                @if ($isSuperAdmin)
                    <div class="rounded-xl bg-primary-50 px-4 py-3 text-sm font-medium text-primary-700 ring-1 ring-inset ring-primary-600/20">
                        This is the Super Admin role — it always holds every permission and cannot be modified or deleted.
                    </div>
                @endif

                @include('roles._permissions', ['checkedPermissions' => $isSuperAdmin ? collect(config('system.screens'))->flatMap(fn ($s, $k) => collect($s['permissions'])->map(fn ($a) => $k.'.'.$a))->all() : $rolePermissions])

                <div class="flex items-center justify-between gap-3 pt-6">
                    <a href="{{ route('roles.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    @if (!$isSuperAdmin)
                        {!! html()->submit('Save changes')->class($submitClasses . ' w-auto px-6 py-2') !!}
                    @endif
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>
