@php
    $activeTab = $activeTab ?? 'details';
    $cpLinks = [
        'details' => ['label' => 'Container Purchase Details', 'route' => 'container-purchases.index', 'icon' => 'package'],
        'models' => ['label' => 'Purchase', 'route' => 'container-purchases.models', 'icon' => 'truck'],
        'invoices' => ['label' => 'Invoice', 'route' => 'container-purchases.invoices', 'icon' => 'receipt'],
        'releases' => ['label' => 'Release', 'route' => 'container-purchases.releases', 'icon' => 'key'],
        'debits' => ['label' => 'Debit Note', 'route' => 'container-purchases.debits', 'icon' => 'badge-dollar-sign'],
        'po-cancels' => ['label' => 'PO Cancel', 'route' => 'container-purchases.po-cancels', 'icon' => 'tag'],
    ];
@endphp

<div class="mb-6 flex flex-wrap items-center gap-1 rounded-xl bg-gray-100 p-1">
    @foreach ($cpLinks as $key => $link)
        <a href="{{ route($link['route']) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition {{ $activeTab === $key ? 'bg-primary-600 text-white shadow-sm' : 'text-gray-600 hover:bg-white hover:text-gray-900' }}">
            @include('components.icons.'.$link['icon'], ['classes' => 'h-4 w-4']) {{ $link['label'] }}
        </a>
    @endforeach
</div>