<x-app-layout :title="'Container Kinds'">
    <div class="mx-auto max-w-7xl space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    @include('components.icons.box', ['classes' => 'h-7 w-7 text-primary-600'])
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Container Kinds</h1>
                        <p class="mt-1 text-sm text-topbar-muted">Manage the container kinds used across your operations.</p>
                    </div>
                </div>
            </div>
            @if (auth()->user()->isSuperAdmin() || auth()->user()->can('container_kinds.add'))
                <a href="{{ route('container-kinds.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M5 12h14" />
                        <path d="M12 5v14" />
                    </svg>
                    New container kind
                </a>
            @endif
        </div>

        @if (session('status'))
            <div class="rounded-xl bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700 ring-1 ring-inset ring-emerald-600/20">
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl bg-red-50 px-4 py-3 text-sm font-medium text-red-700 ring-1 ring-inset ring-red-600/20">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-hidden rounded-2xl bg-card-bg shadow-lg ring-1 ring-card-border">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4 sm:px-8">
                <h2 class="text-lg font-semibold text-gray-800">All Container Kinds</h2>
            </div>
            <div class="p-6 sm:p-8">
                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>

    {!! $dataTable->scripts() !!}
</x-app-layout>
