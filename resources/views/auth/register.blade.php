<x-guest-layout>
    <div class="text-center">
        <h1 class="text-2xl font-bold tracking-tight text-topbar-text sm:text-3xl">Create your account</h1>
        <p class="mt-2 text-sm text-topbar-muted">Join the crew and start moving cargo.</p>
    </div>

    <div class="mt-8 rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-medium text-topbar-text">Full name <span class="text-red-500">*</span></label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    autocomplete="name"
                    class="mt-1.5 block w-full rounded-lg border-0 bg-page-bg px-3 py-2.5 text-topbar-text ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:bg-card-bg focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                    placeholder="John Doe"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-topbar-text">Email address <span class="text-red-500">*</span></label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="username"
                    class="mt-1.5 block w-full rounded-lg border-0 bg-page-bg px-3 py-2.5 text-topbar-text ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:bg-card-bg focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-topbar-text">Password <span class="text-red-500">*</span></label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="mt-1.5 block w-full rounded-lg border-0 bg-page-bg px-3 py-2.5 text-topbar-text ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:bg-card-bg focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-topbar-text">Confirm password <span class="text-red-500">*</span></label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="mt-1.5 block w-full rounded-lg border-0 bg-page-bg px-3 py-2.5 text-topbar-text ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:bg-card-bg focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                    placeholder="••••••••"
                >
            </div>

            <button type="submit" class="w-full rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200">
                Create account
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-topbar-muted">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-primary-600 hover:text-primary-700 hover:underline transition">Sign in</a>
        </p>
    </div>
</x-guest-layout>
