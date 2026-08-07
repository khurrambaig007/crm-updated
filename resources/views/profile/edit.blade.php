<x-app-layout :title="'Profile'">
    <div class="mx-auto max-w-3xl space-y-8">
        <div>
            <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Profile</h1>
            <p class="mt-1 text-sm text-topbar-muted">Manage your account information and password.</p>
        </div>

        @if (session('status'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                {{ session('status') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl bg-card-bg shadow-sm ring-1 ring-card-border">
            <div class="h-32 bg-gradient-to-r from-primary-500 via-primary-400 to-teal-600"></div>
            <div class="px-6 pb-6 sm:px-8">
                <div class="relative -mt-10 flex items-end justify-between">
                    <div class="flex items-center gap-4">
                        <div class="h-20 w-20 rounded-2xl bg-primary-500 shadow-lg ring-4 ring-card-bg">
                            <div class="flex h-full w-full items-center justify-center text-2xl font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        </div>
                        <div>
                            <div class="text-lg font-semibold text-topbar-text">{{ $user->name }}</div>
                            <div class="text-sm text-topbar-muted">{{ $user->email }}</div>
                        </div>
                    </div>
                    <span class="rounded-full bg-page-bg px-3 py-1 text-xs font-medium text-topbar-muted">Member</span>
                </div>
            </div>
        </div>

        <div class="grid gap-6 sm:grid-cols-2">
            <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
                <h2 class="text-base font-semibold text-topbar-text">Profile details</h2>
                <p class="mt-1 text-sm text-topbar-muted">Update your name and email address.</p>

                <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="name" class="block text-sm font-medium text-topbar-text">Full name</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            required
                            autocomplete="name"
                            class="mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-topbar-text">Email address</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            readonly
                            autocomplete="username"
                            class="mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200">
                        Save changes
                    </button>
                </form>
            </div>

            <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
                <h2 class="text-base font-semibold text-topbar-text">Password</h2>
                <p class="mt-1 text-sm text-topbar-muted">Keep your account secure with a strong password.</p>

                <form method="POST" action="{{ route('profile.password') }}" class="mt-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label for="current_password" class="block text-sm font-medium text-topbar-text">Current password</label>
                        <div class="relative mt-1.5">
                            <input
                                id="current_password"
                                name="current_password"
                                type="password"
                                required
                                autocomplete="current-password"
                                class="block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-11 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                            >
                            <button type="button" class="password-toggle absolute inset-y-0 right-0 flex items-center px-3 text-topbar-muted hover:text-topbar-text transition-colors" aria-label="Show password">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon h-5 w-5">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon hidden h-5 w-5">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                    <path d="M6.61 6.61A13.53 13.53 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                    <line x1="2" x2="22" y1="2" y2="22" />
                                </svg>
                            </button>
                        </div>
                        @error('current_password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-topbar-text">New password</label>
                        <div class="relative mt-1.5">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-11 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                            >
                            <button type="button" class="password-toggle absolute inset-y-0 right-0 flex items-center px-3 text-topbar-muted hover:text-topbar-text transition-colors" aria-label="Show password">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon h-5 w-5">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon hidden h-5 w-5">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                    <path d="M6.61 6.61A13.53 13.53 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                    <line x1="2" x2="22" y1="2" y2="22" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div id="password-meter" class="mt-3">
                            <div class="flex gap-1">
                                <span class="password-segment h-1.5 flex-1 rounded-full bg-card-border transition-colors duration-300"></span>
                                <span class="password-segment h-1.5 flex-1 rounded-full bg-card-border transition-colors duration-300"></span>
                                <span class="password-segment h-1.5 flex-1 rounded-full bg-card-border transition-colors duration-300"></span>
                            </div>
                            <p id="password-hint" class="mt-2 text-xs text-topbar-muted">At least 8 characters, with mixed case and numbers/symbols.</p>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-topbar-text">Confirm new password</label>
                        <div class="relative mt-1.5">
                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                class="block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-11 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                            >
                            <button type="button" class="password-toggle absolute inset-y-0 right-0 flex items-center px-3 text-topbar-muted hover:text-topbar-text transition-colors" aria-label="Show password">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon h-5 w-5">
                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon hidden h-5 w-5">
                                    <path d="M9.88 9.88a3 3 0 1 0 4.24 4.24" />
                                    <path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68" />
                                    <path d="M6.61 6.61A13.53 13.53 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61" />
                                    <line x1="2" x2="22" y1="2" y2="22" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200">
                        Update password
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    (function () {
        document.querySelectorAll('.password-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input = btn.parentElement.querySelector('input');
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                btn.querySelector('.eye-icon').classList.toggle('hidden', isPassword);
                btn.querySelector('.eye-off-icon').classList.toggle('hidden', !isPassword);
                btn.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
        });

        const input = document.getElementById('password');
        if (!input) {
            return;
        }

        const segments = document.querySelectorAll('.password-segment');
        const hint = document.getElementById('password-hint');

        const fillColors = ['bg-red-500', 'bg-amber-500', 'bg-emerald-500'];
        const hintColors = ['text-red-600', 'text-amber-600', 'text-emerald-600'];

        function score(pw) {
            let s = 0;
            if (pw.length >= 8) {
                s++;
            }
            if (/[a-z]/.test(pw) && /[A-Z]/.test(pw)) {
                s++;
            }
            if (/\d/.test(pw) || /[^A-Za-z0-9]/.test(pw)) {
                s++;
            }
            return s;
        }

        input.addEventListener('input', function () {
            const pw = input.value;
            const s = score(pw);

            segments.forEach(function (el, i) {
                el.classList.remove('bg-card-border', 'bg-red-500', 'bg-amber-500', 'bg-emerald-500');
                if (i < s) {
                    el.classList.add(fillColors[s - 1]);
                } else {
                    el.classList.add('bg-card-border');
                }
            });

            if (pw.length === 0) {
                hint.textContent = 'At least 8 characters, with mixed case and numbers/symbols.';
                hint.className = 'mt-2 text-xs text-topbar-muted';
            } else if (s === 0) {
                hint.textContent = 'At least 8 characters, with mixed case and numbers/symbols.';
                hint.className = 'mt-2 text-xs text-red-600';
            } else {
                hint.textContent = ['Weak', 'Fair', 'Strong'][s - 1] + ' password.';
                hint.className = 'mt-2 text-xs ' + hintColors[s - 1];
            }
        });
    })();
</script>
