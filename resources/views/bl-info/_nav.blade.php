@php
    $activeTab = $activeTab ?? 'index';
    $blLinks = [
        'index' => ['label' => 'Bl Info', 'route' => 'bl-info.index', 'icon' => 'package'],
        'booking-info' => ['label' => 'Booking Info', 'route' => 'bl-info.booking-info', 'icon' => 'calendar'],
        'release-instructions' => ['label' => 'Release Instructions', 'route' => 'bl-info.release-instructions', 'icon' => 'key'],
        'delivery-order' => ['label' => 'Delivery Order', 'route' => 'bl-info.delivery-order', 'icon' => 'truck'],
        'lock-info' => ['label' => 'Lock Info', 'route' => 'bl-info.lock-info', 'icon' => 'shield'],
        'authorization' => ['label' => 'Authorization', 'route' => 'bl-info.authorization', 'icon' => 'users'],
    ];
@endphp

<div class="mb-6 flex flex-wrap items-center gap-1 rounded-xl bg-gray-100 p-1">
    @foreach ($blLinks as $key => $link)
        <a href="{{ route($link['route'], $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $activeTab === $key ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-white hover:text-gray-900' }}">
            @include('components.icons.'.$link['icon'], ['classes' => 'h-4 w-4']) {{ $link['label'] }}
        </a>
    @endforeach
</div>
