
<x-app-layout :title="'Purchase Invoice'">
    @php
        $isNew = ! $invoice->exists;
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $disabledClasses = 'mt-1 block w-full rounded-md border-0 bg-gray-100 px-2.5 py-1.5 text-sm text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $navBtnClasses = 'inline-flex items-center justify-center rounded-lg p-1.5 text-topbar-muted transition-all hover:bg-primary-50 hover:text-primary-600 disabled:opacity-40 disabled:cursor-not-allowed';
    @endphp
    <style>        
        #details-grid.ag-theme-quartz {
            --ag-row-height: 52px!important;            
            --ag-cell-horizontal-padding: 10px;
            --ag-header-background-color: var(--color-primary-900);
            --ag-header-foreground-color: #ffffff;
            --ag-header-cell-hover-background-color: var(--color-primary-700);
            --ag-header-cell-moving-background-color: var(--color-primary-800);
        }
    </style>
    <div class="mx-auto max-w-full space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.receipt', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Purchase Invoice</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Create and manage purchase invoices and their details.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            <div class="mb-6 flex items-center justify-center gap-1">
                <button type="button" id="nav-first" class="nav-btn {{ $navBtnClasses }}" title="First" data-direction="first" {{ $firstId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m11 17-5-5 5-5"/><path d="m18 17-5-5 5-5"/></svg>
                </button>
                <button type="button" id="nav-prev" class="nav-btn {{ $navBtnClasses }}" title="Previous" data-direction="prev" {{ $prevId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button type="button" id="nav-new" class="{{ $navBtnClasses }} ml-2" title="New" onclick="newRecord()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                </button>
                <button type="button" id="nav-next" class="nav-btn {{ $navBtnClasses }}" title="Next" data-direction="next" {{ $nextId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <button type="button" id="nav-last" class="nav-btn {{ $navBtnClasses }}" title="Last" data-direction="last" {{ $lastId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m13 17 5-5-5-5"/><path d="m6 17 5-5-5-5"/></svg>
                </button>
                <span class="ml-3 text-sm font-medium text-topbar-muted">
                    <span id="nav-current">{{ $current }}</span> of <span id="nav-total">{{ $total }}</span>
                </span>
            </div>

            {!! html()->form($isNew ? 'POST' : 'PATCH', $isNew ? route('purchase-invoices.store') : route('purchase-invoices.update', $invoice))->id('header-form')->class('grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4')->open() !!}

                <div>
                    <label for="transaction_date" class="{{ $labelClasses }}">Trans Date <span class="text-red-500">*</span></label>
                    {!! html()->date('transaction_date', $invoice->transaction_date?->format('Y-m-d'))->class($inputClasses)->required() !!}
                    @error('transaction_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="transaction_number" class="{{ $labelClasses }}">Transaction # <span class="text-red-500">*</span></label>
                    {!! html()->text('transaction_number', $invoice->transaction_number)->class($inputClasses)->required() !!}
                    @error('transaction_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Status', 'status')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="status" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select status</option>
                            <option value="0" {{ old('status', $invoice->status) === 0 ? 'selected' : '' }}>Pending</option>
                            <option value="1" {{ old('status', $invoice->status) === 1 ? 'selected' : '' }}>Approved</option>
                            <option value="2" {{ old('status', $invoice->status) === 2 ? 'selected' : '' }}>Rejected</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="purchase_invoice_date" class="{{ $labelClasses }}">Purchase Invoice Date <span class="text-red-500">*</span></label>
                    {!! html()->date('purchase_invoice_date', $invoice->purchase_invoice_date?->format('Y-m-d'))->class($inputClasses)->required() !!}
                    @error('purchase_invoice_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="invoice_number" class="{{ $labelClasses }}">Invoice # <span class="text-red-500">*</span></label>
                    {!! html()->text('invoice_number', $invoice->invoice_number)->class($inputClasses)->required() !!}
                    @error('invoice_number')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="period_from" class="{{ $labelClasses }}">Period From <span class="text-red-500">*</span></label>
                    {!! html()->date('period_from', $invoice->period_from?->format('Y-m-d'))->class($inputClasses)->required() !!}
                    @error('period_from')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="period_to" class="{{ $labelClasses }}">Period To <span class="text-red-500">*</span></label>
                    {!! html()->date('period_to', $invoice->period_to?->format('Y-m-d'))->class($inputClasses)->required() !!}
                    @error('period_to')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Vendor', 'vendor_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="vendor_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select a vendor</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ old('vendor_id', $invoice->vendor_id) == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('vendor_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Port', 'port_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="port_id" id="port_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="updatePortName(this)">
                            <option value="">Select a port</option>
                            @foreach ($ports as $port)
                                <option value="{{ $port->id }}" data-name="{{ $port->city }} ({{ $port->country }})" {{ old('port_id', $invoice->port_id) == $port->id ? 'selected' : '' }}>{{ $port->city }} ({{ $port->country }})</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('port_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Port Name</label>
                    <input type="text" id="port_name_display" class="{{ $disabledClasses }}" disabled value="{{ $invoice->port ? $invoice->port->city . ' (' . $invoice->port->country . ')' : '' }}">
                </div>

                <div>
                    <label for="payment_center" class="{{ $labelClasses }}">Payment Center</label>
                    {!! html()->text('payment_center', $invoice->payment_center)->class($inputClasses) !!}
                    @error('payment_center')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div></div>

                <div>
                    {!! html()->label('Settlement Type', 'settlement_type_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="settlement_type_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select a type</option>
                            @foreach ($settlementTypes as $type)
                                <option value="{{ $type->id }}" {{ old('settlement_type_id', $invoice->settlement_type_id) == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('settlement_type_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Sub Company', 'sub_company_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="sub_company_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select a company</option>
                            @foreach ($subCompanies as $company)
                                <option value="{{ $company->id }}" {{ old('sub_company_id', $invoice->sub_company_id) == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('sub_company_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div></div>
                <div></div>

                <div class="col-span-full flex items-center justify-between gap-3 pt-6">
                    <a href="{{ route('purchase-invoices.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    {!! html()->submit($isNew ? 'Create invoice' : 'Save changes')->class($submitClasses . ' w-auto px-6 py-2') !!}
                </div>

            {!! html()->form()->close() !!}
        </div>

        <div id="details-section" class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8 @if($isNew) hidden @endif">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Invoice Details</h2>
                <button type="button" onclick="window.PIG.addRow()" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-900 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Add Row
                </button>
            </div>

            <div id="details-grid" class="ag-theme-quartz" style="width: 100%; height: 400px;"></div>
        </div>
    </div>

    
    <script type="text/javascript">
        const invoiceId = {{ $invoice->id ?? 'null' }};
        const isNew = {{ $isNew ? 'true' : 'false' }};
        const navData = {
            total: {{ $total }},
            current: {{ $current }},
            firstId: {{ $firstId ?? 'null' }},
            lastId: {{ $lastId ?? 'null' }},
            prevId: {{ $prevId ?? 'null' }},
            nextId: {{ $nextId ?? 'null' }},
        };

        const gridConfig = {
            invoiceId: invoiceId,
            rates: @json($rates),
            charges: @json($charges->pluck('name')),
            types: @json($containerTypes->pluck('name')),
            containerTypes: @json($containerTypes->pluck('name')),
            sizes: @json($containerSizes->pluck('size')),
            currencyCodes: @json($currencyCodes),
            details: @json($invoice->details->map(fn ($d) => $d->toArray())),
        };

        document.addEventListener('DOMContentLoaded', function () {
            window.PIG.init(document.getElementById('details-grid'), gridConfig);
            updateNavButtons();
        });

        function updatePortName(select) {
            const selected = select.options[select.selectedIndex];
            const name = selected.getAttribute('data-name') || '';
            document.getElementById('port_name_display').value = name;
        }

        function newRecord() {
            const form = document.getElementById('header-form');
            form.querySelectorAll('input:not([type="hidden"]), select').forEach(el => {
                if (el.name === '_token') return;
                el.value = '';
            });
            form.action = '{{ route('purchase-invoices.store') }}';
            form.method = 'POST';
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();

            document.getElementById('port_name_display').value = '';
            document.getElementById('nav-current').textContent = navData.total + 1;
            document.getElementById('nav-total').textContent = navData.total;
            document.getElementById('details-section').style.display = 'none';

            window.PIG.setData([]);
            window.history.replaceState({}, '', '{{ route('purchase-invoices.create') }}');

            navData.current = navData.total + 1;
            navData.firstId = null;
            navData.lastId = null;
            navData.prevId = null;
            navData.nextId = null;
            updateNavButtons();
        }

        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', async function (e) {
                e.preventDefault();
                const direction = this.dataset.direction;
                const targetId = navData[direction + 'Id'];
                if (!targetId) return;

                try {
                    const resp = await fetch(`/purchase-invoices/${targetId}/navigate`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await resp.json();
                    if (resp.ok) {
                        updateFormWithData(data);
                    }
                } catch (e) {
                    console.error('Navigation error:', e);
                }
            });
        });

        function updateFormWithData(data) {
            const inv = data.invoice;
            document.querySelector('input[name="transaction_date"]').value = inv.transaction_date || '';
            document.querySelector('input[name="transaction_number"]').value = inv.transaction_number || '';
            document.querySelector('select[name="status"]').value = inv.status ?? '';
            document.querySelector('input[name="purchase_invoice_date"]').value = inv.purchase_invoice_date || '';
            document.querySelector('input[name="invoice_number"]').value = inv.invoice_number || '';
            document.querySelector('input[name="period_from"]').value = inv.period_from || '';
            document.querySelector('input[name="period_to"]').value = inv.period_to || '';
            document.querySelector('select[name="vendor_id"]').value = inv.vendor_id || '';
            document.querySelector('select[name="port_id"]').value = inv.port_id || '';
            document.querySelector('input[name="payment_center"]').value = inv.payment_center || '';
            document.querySelector('select[name="settlement_type_id"]').value = inv.settlement_type_id || '';
            document.querySelector('select[name="sub_company_id"]').value = inv.sub_company_id || '';

            const portSelect = document.querySelector('select[name="port_id"]');
            if (portSelect) updatePortName(portSelect);

            const form = document.getElementById('header-form');
            form.action = `/purchase-invoices/${inv.id}`;
            form.method = 'POST';
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);
            } else {
                methodInput.value = 'PATCH';
            }

            document.getElementById('nav-current').textContent = data.current;
            document.getElementById('nav-total').textContent = data.total;

            navData.total = data.total;
            navData.current = data.current;
            navData.firstId = data.firstId;
            navData.lastId = data.lastId;
            navData.prevId = data.prevId;
            navData.nextId = data.nextId;

            gridConfig.invoiceId = inv.id;
            document.getElementById('details-section').style.display = '';
            window.PIG.setData(inv.details || []);
            updateNavButtons();

            window.history.replaceState({}, '', `/purchase-invoices/${inv.id}/edit`);
        }

        function updateNavButtons() {
            const updateBtn = (id, selector) => {
                const el = document.getElementById(selector);
                if (!el) return;
                if (id) {
                    el.removeAttribute('disabled');
                    el.style.pointerEvents = '';
                    el.style.opacity = '';
                } else {
                    el.setAttribute('disabled', '');
                    el.style.pointerEvents = 'none';
                    el.style.opacity = '0.4';
                }
            };
            updateBtn(navData.firstId, 'nav-first');
            updateBtn(navData.prevId, 'nav-prev');
            updateBtn(navData.nextId, 'nav-next');
            updateBtn(navData.lastId, 'nav-last');
        }
    </script>
</x-app-layout>
