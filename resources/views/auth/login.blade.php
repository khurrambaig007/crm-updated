<x-guest-layout>
    <div class="text-center">
        <h1 class="text-2xl font-bold tracking-tight text-topbar-text sm:text-3xl">Welcome back</h1>
        <p class="mt-2 text-sm text-topbar-muted">Sign in to continue to your workspace.</p>
    </div>

    <div class="mt-8 rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-medium text-topbar-text">Email address</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    class="mt-1.5 block w-full rounded-lg border-0 bg-page-bg px-3 py-2.5 text-topbar-text ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:bg-card-bg focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                    placeholder="you@example.com"
                >
                @error('email')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-topbar-text">Password</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-1.5 block w-full rounded-lg border-0 bg-page-bg px-3 py-2.5 text-topbar-text ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:bg-card-bg focus:ring-2 focus:ring-inset focus:ring-primary-500 transition"
                    placeholder="••••••••"
                >
                @error('password')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <label for="remember" class="flex items-center gap-2 text-sm text-topbar-muted">
                    <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-card-border text-primary-500 focus:ring-primary-500 transition">
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" class="text-sm font-medium text-primary-600 hover:text-primary-700 hover:underline transition">Forgot password?</a>
            </div>

            <button type="submit" class="w-full rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200">
                Sign in
            </button>
        </form>

        <p class="mt-6 text-center text-sm text-topbar-muted">
            Don't have an account?
            <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-700 hover:underline transition">Create one now</a>
        </p>
    </div>
</x-guest-layout>
