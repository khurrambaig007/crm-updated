<x-app-layout :title="$tabTitle">
    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => 'Bill of lading header for booking'])

        @include('bl-info._nav')

        <div class="rounded bg-card-bg px-6 py-16 text-center shadow-sm ring-1 ring-card-border sm:px-10 sm:py-20">
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-primary-50 text-primary-600">
                @include('components.icons.list', ['classes' => 'h-7 w-7'])
            </div>
            <h2 class="mt-5 text-lg font-semibold text-topbar-text">{{ $tabTitle }}</h2>
            <p class="mx-auto mt-1 max-w-md text-sm text-topbar-muted">{{ $tabDescription }}</p>
            <span class="mt-5 inline-flex items-center rounded-full bg-amber-50 px-3 py-1 text-xs font-medium text-amber-700 ring-1 ring-amber-200">Coming soon</span>
        </div>
    </div>
</x-app-layout>
