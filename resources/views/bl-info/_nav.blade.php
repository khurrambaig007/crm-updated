@php
    $activeTab = $activeTab ?? 'index';

    $blChildren = [
        'booking-info' => ['label' => 'Booking Info', 'route' => 'bl-info.booking-info', 'icon' => 'calendar'],
        'release-instructions' => ['label' => 'Release Instructions', 'route' => 'bl-info.release-instructions', 'icon' => 'key'],
        'delivery-order' => ['label' => 'Delivery Order', 'route' => 'bl-info.delivery-order', 'icon' => 'truck'],
        'lock-info' => ['label' => 'Lock Info', 'route' => 'bl-info.lock-info', 'icon' => 'shield'],
        'authorization' => ['label' => 'Authorization', 'route' => 'bl-info.authorization', 'icon' => 'users'],
    ];

    $futureItems = [
        'refnos' => ['label' => "Ref NO's", 'icon' => 'tag'],
        'stamps' => ['label' => 'Stamps', 'icon' => 'landmark'],
    ];

    $hasActiveChild = array_key_exists($activeTab, $blChildren);
    $parentActive = $activeTab === 'index';
    // Wayfinding only: the parent pill stays tinted when any child tab is
    // active, but the dropdown itself always starts closed on page load.
    $inSection = $parentActive || $hasActiveChild;
@endphp

<nav aria-label="Bill of lading sections"
    class="mb-6 rounded-2xl bg-card-bg p-2 shadow-sm ring-1 ring-card-border">
    <div class="flex flex-wrap items-center gap-1">
        {{-- Parent: BL Info (dropdown) --}}
        <div class="relative" data-bl-nav-parent>
            <div class="flex items-center gap-1 rounded-xl {{ $inSection ? 'bg-primary-50' : '' }}">
                <a href="{{ route('bl-info.index', $booking) }}"
                    class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold {{ $inSection ? 'text-primary-700' : 'text-topbar-text hover:bg-page-bg' }} transition-colors">
                    <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600 {{ $inSection ? 'ring-1 ring-primary-200' : '' }}">
                        @include('components.icons.package', ['classes' => 'h-4 w-4'])
                    </span>
                    <span class="whitespace-nowrap">BL Info</span>
                </a>
                <button type="button" data-bl-nav-toggle aria-label="Toggle BL Info sections"
                    aria-expanded="false"
                    class="rounded-lg p-2 text-topbar-muted transition-colors hover:bg-page-bg hover:text-topbar-text focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    <svg class="h-4 w-4 transition-transform duration-200 ease-in-out rotate-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m6 9 6 6 6-6" />
                    </svg>
                </button>
            </div>

            <div class="absolute left-0 top-full z-20 mt-1 w-72 rounded-xl bg-card-bg p-1 shadow-lg ring-1 ring-card-border transition-all duration-200 ease-in-out hidden opacity-0 -translate-y-1">
                <div class="space-y-0.5">
                    @foreach ($blChildren as $key => $child)
                        <a href="{{ route($child['route'], $booking) }}"
                            class="relative flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition-all duration-150 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-inset {{ $activeTab === $key ? 'bg-primary-50 text-primary-700' : 'text-topbar-muted hover:bg-page-bg hover:text-topbar-text' }}">
                            @if ($activeTab === $key)
                                <span class="absolute left-0 top-1/2 h-4 w-0.5 -translate-y-1/2 rounded-full bg-primary-500"></span>
                            @endif
                            @include('components.icons.'.$child['icon'], ['classes' => 'h-4 w-4 shrink-0'])
                            <span class="whitespace-nowrap">{{ $child['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mx-1 h-6 w-px bg-card-border"></div>

        {{-- Sibling entity: the bill of lading particulars document --}}
        <a href="{{ route('bl-info.bl-detail', $booking) }}"
            aria-current="{{ $activeTab === 'bl-detail' ? 'page' : 'false' }}"
            class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition-colors {{ $activeTab === 'bl-detail' ? 'bg-primary-50 text-primary-700' : 'text-topbar-text hover:bg-page-bg' }}">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600 {{ $activeTab === 'bl-detail' ? 'ring-1 ring-primary-200' : '' }}">
                @include('components.icons.receipt', ['classes' => 'h-4 w-4'])
            </span>
            <span class="whitespace-nowrap">BL Detail</span>
        </a>

        <div class="mx-1 h-6 w-px bg-card-border"></div>

        {{-- Sibling entity: the container list --}}
        <a href="{{ route('bl-info.container-info', $booking) }}"
            aria-current="{{ $activeTab === 'container-info' ? 'page' : 'false' }}"
            class="flex items-center gap-2.5 rounded-xl px-3 py-2.5 text-sm font-semibold transition-colors {{ $activeTab === 'container-info' ? 'bg-primary-50 text-primary-700' : 'text-topbar-text hover:bg-page-bg' }}">
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600 {{ $activeTab === 'container-info' ? 'ring-1 ring-primary-200' : '' }}">
                @include('components.icons.box', ['classes' => 'h-4 w-4'])
            </span>
            <span class="whitespace-nowrap">Container Info</span>
        </a>

        <div class="mx-1 h-6 w-px bg-card-border"></div>

        {{-- Future top-level entities --}}
        @foreach ($futureItems as $item)
            <div class="group flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium text-topbar-muted opacity-60 select-none cursor-not-allowed transition-opacity duration-150 hover:opacity-75">
                @include('components.icons.'.$item['icon'], ['classes' => 'h-4 w-4 shrink-0'])
                <span class="whitespace-nowrap">{{ $item['label'] }}</span>
                <span class="rounded-full bg-amber-50 px-2 py-0.5 text-[11px] font-semibold text-amber-700 ring-1 ring-amber-200">Soon</span>
            </div>
        @endforeach
    </div>
</nav>