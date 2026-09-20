<x-app-layout :title="'Un-Authorized'">
    <div class="mx-auto flex max-w-xl flex-col items-center justify-center px-4 py-16 text-center">
        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-red-50 text-red-500 ring-1 ring-inset ring-red-100">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10">
                <path d="M18.5 22H17v-2.5a2 2 0 0 1 2-2h.5a2 2 0 0 1 2 2V22h-3Z" />
                <path d="M20 11V8a8 8 0 1 0-16 0v3" />
                <path d="M12 13v4" />
                <path d="M22 22H2" />
            </svg>
        </div>

        <h1 class="mt-6 text-3xl font-bold tracking-tight text-topbar-text">403 — Un-Authorized</h1>
        <p class="mt-2 text-sm text-topbar-muted">Your account has not been assigned a role or permission for this page. If you believe this is a mistake, contact your administrator.</p>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-red-500">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" />
                        <path d="m16 17 5-5-5-5" />
                        <path d="M21 12H9" />
                    </svg>
                    Sign out
                </button>
            </form>
            @endauth
            <a href="javascript:history.back()" class="inline-flex items-center justify-center gap-2 rounded-lg px-5 py-2.5 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>
                Go back
            </a>
        </div>
    </div>
</x-app-layout>
