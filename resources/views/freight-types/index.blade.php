<x-app-layout :title="'Freight Types'">
    <div class="mx-auto max-w-full space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    @include('components.icons.tag', ['classes' => 'h-7 w-7 text-primary-600'])
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Freight Types</h1>
                        <p class="mt-1 text-sm text-topbar-muted">Manage your freight types.</p>
                    </div>
                </div>
            </div>
            @if (auth()->user()->isSuperAdmin() || auth()->user()->can('freight_types.add'))
                <a href="{{ route('freight-types.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-primary-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-primary-600/25 transition-all hover:bg-primary-700 hover:shadow-primary-700/30">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14" /><path d="M12 5v14" /></svg>
                    New freight type
                </a>
            @endif
        </div>

        @if (session('status'))
            <x-alerts :type="'success'" :message="session('status')" />
        @endif
        @if (session('error'))
            <x-alerts :type="'error'" :message="session('error')" />
        @endif

        <div class="overflow-hidden rounded-2xl bg-card-bg shadow-lg ring-1 ring-card-border">
            <div class="border-b border-card-border px-6 py-5 sm:px-8">
                <h2 class="text-lg font-semibold text-topbar-text">All Freight Types</h2>
            </div>
            <div class="p-6 sm:p-8">
                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>

    {!! $dataTable->scripts() !!}
</x-app-layout>
