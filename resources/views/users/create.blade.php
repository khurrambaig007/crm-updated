<x-app-layout :title="'New user'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $submitClasses = 'w-full rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $themes = [
            'slate-orange' => 'Orange',
            'slate-blue' => 'Blue',
            'slate-teal' => 'Teal',
            'slate-violet' => 'Violet',
        ];
    @endphp

    <div class="mx-auto max-w-2xl space-y-8">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">New user</h1>
            <p class="mt-1 text-sm text-topbar-muted">Create a new account for a member of your team.</p>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form('POST', route('users.store'))->class('space-y-5')->open() !!}

                <div>
                    {!! html()->label('Full name', 'name')->class($labelClasses) !!}
                    {!! html()->text('name')->class($inputClasses)->required()->attribute('autocomplete', 'name') !!}
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Email address', 'email')->class($labelClasses) !!}
                    {!! html()->email('email')->class($inputClasses)->required()->attribute('autocomplete', 'username') !!}
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Phone number', 'phone')->class($labelClasses) !!}
                    {!! html()->text('phone')->class($inputClasses)->attribute('autocomplete', 'tel') !!}
                    @error('phone')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Password', 'password')->class($labelClasses) !!}
                    {!! html()->password('password')->class($inputClasses)->required()->attribute('autocomplete', 'new-password') !!}
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Confirm password', 'password_confirmation')->class($labelClasses) !!}
                    {!! html()->password('password_confirmation')->class($inputClasses)->required()->attribute('autocomplete', 'new-password') !!}
                </div>

                <div>
                    {!! html()->label('Theme', 'theme')->class($labelClasses) !!}
                    <div class="relative">
                        {!! html()->select('theme', $themes, 'slate-orange')->class($selectClasses . ' appearance-none cursor-pointer') !!}
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('theme')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Role', 'role')->class($labelClasses) !!}
                    <div class="relative">
                        {!! html()->select('role', $roles, null)->class($selectClasses . ' appearance-none cursor-pointer')->placeholder('Select a role') !!}
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('role')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-6">
                    <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    {!! html()->submit('Create user')->class($submitClasses) !!}
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>
