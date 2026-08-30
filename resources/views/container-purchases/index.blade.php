<x-app-layout>
    @php
        $isNew = $isNew ?? false;
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $radioClasses = 'h-4 w-4 rounded-full border-topbar-border text-primary-600 focus:ring-primary-500';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $chevron = '<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg></div>';
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                <span class="text-2xl">@include('components.icons.package')</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Container Purchase</h1>
                <p class="text-sm text-gray-500">Manage container purchase details, purchase, invoice, release, debit note and PO cancel.</p>
            </div>
        </div>

        <div class="cp-tabs mb-6 flex flex-wrap items-center gap-1 rounded-xl bg-gray-100 p-1" data-active="bg-primary-600 text-white shadow-sm" data-inactive="text-gray-600 hover:bg-white hover:text-gray-900">
            <a href="#container-purchase-details" class="cp-tab-link inline-flex items-center gap-2 rounded-lg bg-primary-600 px-4 py-2 text-sm font-medium text-white shadow-sm">
                @include('components.icons.package', ['classes' => 'h-4 w-4']) Container Purchase Details
            </a>
            <a href="#purchase" class="cp-tab-link inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white hover:text-gray-900">
                @include('components.icons.truck', ['classes' => 'h-4 w-4']) Purchase
            </a>
            <a href="#invoice" class="cp-tab-link inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white hover:text-gray-900">
                @include('components.icons.receipt', ['classes' => 'h-4 w-4']) Invoice
            </a>
            <a href="#release" class="cp-tab-link inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white hover:text-gray-900">
                @include('components.icons.key', ['classes' => 'h-4 w-4']) Release
            </a>
            <a href="#debit-note" class="cp-tab-link inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white hover:text-gray-900">
                @include('components.icons.badge-dollar-sign', ['classes' => 'h-4 w-4']) Debit Note
            </a>
            <a href="#po-cancel" class="cp-tab-link inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-gray-600 hover:bg-white hover:text-gray-900">
                @include('components.icons.tag', ['classes' => 'h-4 w-4']) PO Cancel
            </a>
        </div>

        <div>
                @include('container-purchases.partials.details')

                <div class="cp-tab-pane hidden" id="purchase">
                    <div class="overflow-hidden rounded-2xl bg-card-bg shadow-sm ring-1 ring-card-border">
                        <div class="p-6 overflow-x-auto">
                            {!! $purchaseTable->table() !!}
                        </div>
                    </div>
                </div>

                <div class="cp-tab-pane hidden" id="invoice">
                    <div class="overflow-hidden rounded-2xl bg-card-bg shadow-sm ring-1 ring-card-border">
                        <div class="p-6 overflow-x-auto">
                            {!! $invoiceTable->table() !!}
                        </div>
                    </div>
                </div>

                <div class="cp-tab-pane hidden" id="release">
                    <div class="overflow-hidden rounded-2xl bg-card-bg shadow-sm ring-1 ring-card-border">
                        <div class="p-6 overflow-x-auto">
                            {!! $releaseTable->table() !!}
                        </div>
                    </div>
                </div>

                <div class="cp-tab-pane hidden" id="debit-note">
                    <div class="overflow-hidden rounded-2xl bg-card-bg shadow-sm ring-1 ring-card-border">
                        <div class="p-6 overflow-x-auto">
                            {!! $debitTable->table() !!}
                        </div>
                    </div>
                </div>

                <div class="cp-tab-pane hidden" id="po-cancel">
                    <div class="overflow-hidden rounded-2xl bg-card-bg shadow-sm ring-1 ring-card-border">
                        <div class="p-6 overflow-x-auto">
                            {!! $poCancelTable->table() !!}
                        </div>
                    </div>
                </div>
            </div>
    </div>

    @push('scripts')
    @php
        $cpRatesJson = json_encode($rates);
        $cpStoreUrl = route('container-purchases.store');
        $cpCreateUrl = route('container-purchases.create');
    @endphp
    <script>
        const cpConfig = {
            rates: {!! $cpRatesJson !!},
            storeUrl: '{{ $cpStoreUrl }}',
            createUrl: '{{ $cpCreateUrl }}',
        };

        document.getElementById('cp-currency').addEventListener('change', function () {
            const rate = (cpConfig.rates && cpConfig.rates[this.value]) || 1;
            document.getElementById('cp-rate').value = rate;
            document.getElementById('cp-currency-code').value = this.value;
        });
    </script>

    {!! $dataTable->scripts() !!}
    {!! $purchaseTable->scripts() !!}
    {!! $invoiceTable->scripts() !!}
    {!! $releaseTable->scripts() !!}
    {!! $debitTable->scripts() !!}
    {!! $poCancelTable->scripts() !!}
    @endpush
</x-app-layout>
