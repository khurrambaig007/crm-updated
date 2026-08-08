<x-app-layout :title="'Access denied'">
    <div class="mx-auto flex max-w-xl flex-col items-center justify-center px-4 py-16 text-center">
        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-red-50 text-red-500 ring-1 ring-inset ring-red-100">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-10 w-10">
                <path d="M18.5 22H17v-2.5a2 2 0 0 1 2-2h.5a2 2 0 0 1 2 2V22h-3Z" />
                <path d="M20 11V8a8 8 0 1 0-16 0v3" />
                <path d="M12 13v4" />
                <path d="M22 22H2" />
            </svg>
        </div>

        <h1 class="mt-6 text-3xl font-bold tracking-tight text-topbar-text">403 — Access denied</h1>
        <p class="mt-2 text-sm text-topbar-muted">You don't have permission to view this page. If you believe this is a mistake, contact your administrator.</p>

        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-900 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md transition-all duration-200">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                    <path d="M9 22V12h6v10" />
                </svg>
                Go to dashboard
            </a>
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
