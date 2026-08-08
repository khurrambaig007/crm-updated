<x-app-layout :title="'Settings'">
    <div class="mx-auto max-w-7xl space-y-8">
        <div>
            <div class="flex items-center gap-3">
                @include('components.icons.settings', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Settings</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Manage your workspace settings.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            <div class="flex h-48 items-center justify-center rounded-xl bg-page-bg ring-1 ring-inset ring-card-border">
                <span class="text-sm text-topbar-muted">Settings screen — coming soon.</span>
            </div>
        </div>
    </div>
</x-app-layout>
