
<x-app-layout :title="'Agent Receipt Payment'">
    @php
        $isNew = ! $arp->exists;
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $disabledClasses = 'mt-1 block w-full rounded-md border-0 bg-gray-100 px-2.5 py-1.5 text-sm text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $navBtnClasses = 'inline-flex items-center justify-center rounded-lg p-1.5 text-topbar-muted transition-all hover:bg-primary-50 hover:text-primary-600 disabled:opacity-40 disabled:cursor-not-allowed';

        $modes = [1 => 'Receipt', 2 => 'Payment'];
        $subModes = [1 => 'On Account', 2 => 'On Other', 3 => 'SI', 4 => 'Container Sale', 5 => 'Customer Opening'];
        $actModes = [1 => 'Cash', 2 => 'Bank', 3 => 'Other'];
    @endphp

    <div class="mx-auto max-w-full space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.receipt', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Agent Receipt Payment</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Record and manage agent receipts and payments.</p>
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
                    <span id="nav-current-2">{{ $current }}</span> of <span id="nav-total-2">{{ $total }}</span>
                </span>
            </div>

            {!! html()->form($isNew ? 'POST' : 'PATCH', $isNew ? route('agent-receipt-payments.store') : route('agent-receipt-payments.update', $arp))->id('header-form')->class('space-y-8')->open() !!}

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="transaction_no" class="{{ $labelClasses }}">Transaction # <span class="text-red-500">*</span></label>
                        {!! html()->text('transaction_no', $arp->transaction_no)->class($inputClasses)->required() !!}
                        @error('transaction_no')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="receipt_date" class="{{ $labelClasses }}">Date</label>
                        {!! html()->date('receipt_date', $arp->receipt_date?->format('Y-m-d'))->class($inputClasses) !!}
                        @error('receipt_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Agent', 'agent')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="agent" id="agent" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="updateAgentName(this)">
                                <option value="">Select an agent</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->code }}" data-name="{{ $agent->name }}" {{ old('agent', $arp->agent) == $agent->code ? 'selected' : '' }}>{{ $agent->code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                        </div>
                        @error('agent')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClasses }}">Agent Name</label>
                        <input type="text" id="agent_name_display" class="{{ $disabledClasses }}" disabled value="{{ $agents->firstWhere('code', $arp->agent)?->name }}">
                    </div>

                    <div>
                        {!! html()->label('Mode', 'mode')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="mode" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select mode</option>
                                @foreach ($modes as $value => $label)
                                    <option value="{{ $value }}" {{ old('mode', $arp->mode) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                        </div>
                        @error('mode')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Sub Mode', 'sub_mode')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="sub_mode" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select sub mode</option>
                                @foreach ($subModes as $value => $label)
                                    <option value="{{ $value }}" {{ old('sub_mode', $arp->sub_mode) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                        </div>
                        @error('sub_mode')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Currency', 'currency')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="currency" id="currency" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="updateExchangeRate(this)">
                                <option value="">Select currency</option>
                                @foreach ($currencyCodes as $code)
                                    <option value="{{ $code }}" {{ old('currency', $arp->currency ?? 'USD') == $code ? 'selected' : '' }}>{{ $code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="m6 9 6 6 6-6" />
                                </svg>
                            </div>
                        </div>
                        @error('currency')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="exchange_rate" class="{{ $labelClasses }}">Exchange Rate</label>
                        {!! html()->number('exchange_rate', $arp->exchange_rate ?? 1)->class($inputClasses)->attribute('min', 0)->attribute('step', '0.000001') !!}
                        @error('exchange_rate')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-full">
                        <label for="remarks" class="{{ $labelClasses }}">Remarks</label>
                        {!! html()->text('remarks', $arp->remarks)->class($inputClasses) !!}
                        @error('remarks')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="border-t border-card-border pt-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">Financial Info</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            {!! html()->label('Act Mod', 'act_mode')->class($labelClasses) !!}
                            <div class="relative">
                                <select name="act_mode" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                    <option value="">Select</option>
                                    @foreach ($actModes as $value => $label)
                                        <option value="{{ $value }}" {{ old('act_mode', $arp->act_mode) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </div>
                            </div>
                            @error('act_mode')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bank_charges" class="{{ $labelClasses }}">Bank Charges</label>
                            {!! html()->number('bank_charges', $arp->bank_charges ?? 0)->class($inputClasses)->attribute('min', 0)->attribute('step', '0.01') !!}
                            @error('bank_charges')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ac_code" class="{{ $labelClasses }}">A/C Code</label>
                            {!! html()->text('ac_code', $arp->ac_code)->class($inputClasses) !!}
                            @error('ac_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="{{ $labelClasses }}">Description</label>
                            <input type="text" id="ac_code_display" class="{{ $disabledClasses }}" disabled value="{{ $arp->ac_code }}">
                        </div>

                        <div>
                            <label for="cheque_no" class="{{ $labelClasses }}">Cheque No</label>
                            {!! html()->text('cheque_no', $arp->cheque_no)->class($inputClasses) !!}
                            @error('cheque_no')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="cheque_date" class="{{ $labelClasses }}">Cheque Date</label>
                            {!! html()->date('cheque_date', $arp->cheque_date?->format('Y-m-d'))->class($inputClasses) !!}
                            @error('cheque_date')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="gain_loss" class="{{ $labelClasses }}">Gain/Loss</label>
                            {!! html()->number('gain_loss', $arp->gain_loss ?? 0)->class($inputClasses)->attribute('step', '0.01') !!}
                            @error('gain_loss')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-card-border pt-6">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div></div>
                        <div></div>
                        <div>
                            <label for="total_amount" class="{{ $labelClasses }} text-right">Total Amount</label>
                            {!! html()->number('total_amount', $arp->total_amount ?? 0)->class($inputClasses . ' text-right')->attribute('min', 0)->attribute('step', '0.01') !!}
                            @error('total_amount')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="total_amount_1" class="{{ $labelClasses }} text-right">Total Amount ($)</label>
                            {!! html()->number('total_amount_1', $arp->total_amount_1 ?? 0)->class($inputClasses . ' text-right')->attribute('min', 0)->attribute('step', '0.01') !!}
                            @error('total_amount_1')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-card-border pt-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">Approval</h2>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label for="approved_by" class="{{ $labelClasses }}">Approved By</label>
                            {!! html()->text('approved_by', $arp->approved_by)->class($inputClasses)->disabled(! $isNew) !!}
                            @error('approved_by')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="approved_on" class="{{ $labelClasses }}">Approved On</label>
                            {!! html()->text('approved_on', $arp->approved_on)->class($inputClasses)->disabled(! $isNew) !!}
                            @error('approved_on')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-end">
                            <label class="inline-flex cursor-pointer items-center gap-3">
                                <input type="checkbox" id="approve-switch" class="peer sr-only" {{ $arp->approved_by ? 'checked' : '' }} onchange="toggleApprove(this)">
                                <span class="relative h-6 w-11 rounded-full bg-gray-300 transition-colors peer-checked:bg-emerald-500 after:absolute after:left-1 after:top-1 after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-transform peer-checked:after:translate-x-5"></span>
                                <span class="text-sm font-medium text-topbar-text">Approve</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-span-full flex items-center justify-between gap-3 pt-6">
                    <a href="{{ route('agent-receipt-payments.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    {!! html()->submit($isNew ? 'Create record' : 'Save changes')->class($submitClasses . ' w-auto px-6 py-2') !!}
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>

    <script type="text/javascript">
        const arpId = {{ $arp->id ?? 'null' }};
        const isNew = {{ $isNew ? 'true' : 'false' }};
        const navData = {
            total: {{ $total }},
            current: {{ $current }},
            firstId: {{ $firstId ?? 'null' }},
            lastId: {{ $lastId ?? 'null' }},
            prevId: {{ $prevId ?? 'null' }},
            nextId: {{ $nextId ?? 'null' }},
        };
        const rates = @json($rates);

        document.addEventListener('DOMContentLoaded', function () {
            updateNavButtons();

            const totalInput = document.querySelector('input[name="total_amount"]');
            const rateInput = document.querySelector('input[name="exchange_rate"]');
            if (totalInput) totalInput.addEventListener('input', recalcTotal);
            if (rateInput) rateInput.addEventListener('input', recalcTotal);

            const chequeInput = document.querySelector('input[name="cheque_no"]');
            if (chequeInput) chequeInput.addEventListener('input', maskChequeNo);
        });

        function maskChequeNo(e) {
            const input = e.target;
            const digits = input.value.replace(/\D/g, '').slice(0, 12);
            input.value = digits.replace(/(\d{4})(?=\d)/g, '$1-');
        }

        function updateAgentName(select) {
            const selected = select.options[select.selectedIndex];
            const name = selected.getAttribute('data-name') || '';
            document.getElementById('agent_name_display').value = name;
        }

        function updateExchangeRate(select) {
            const code = select.value;
            const rate = rates[code];
            if (rate) {
                document.querySelector('input[name="exchange_rate"]').value = rate;
            }
            recalcTotal();
        }

        function recalcTotal() {
            const amount = parseFloat(document.querySelector('input[name="total_amount"]').value) || 0;
            const rate = parseFloat(document.querySelector('input[name="exchange_rate"]').value) || 0;
            document.querySelector('input[name="total_amount_1"]').value = rate ? +(amount / rate).toFixed(2) : 0;
        }

        function newRecord() {
            const form = document.getElementById('header-form');
            form.querySelectorAll('input:not([type="hidden"]), select').forEach(el => {
                if (el.name === '_token') return;
                el.value = '';
            });
            form.action = '{{ route('agent-receipt-payments.store') }}';
            form.method = 'POST';
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();

            document.getElementById('agent_name_display').value = '';
            document.getElementById('ac_code_display').value = '';
            updateCounters(navData.total + 1, navData.total);

            window.history.replaceState({}, '', '{{ route('agent-receipt-payments.create') }}');

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
                    const resp = await fetch(`/agent-receipt-payments/${targetId}/navigate`, {
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
            const rec = data.arp;
            document.querySelector('input[name="transaction_no"]').value = rec.transaction_no || '';
            document.querySelector('input[name="receipt_date"]').value = rec.receipt_date || '';
            document.querySelector('select[name="agent"]').value = rec.agent || '';
            document.querySelector('select[name="mode"]').value = rec.mode ?? '';
            document.querySelector('select[name="sub_mode"]').value = rec.sub_mode ?? '';
            document.querySelector('select[name="currency"]').value = rec.currency || '';
            document.querySelector('input[name="exchange_rate"]').value = rec.exchange_rate ?? 1;
            document.querySelector('input[name="remarks"]').value = rec.remarks || '';
            document.querySelector('select[name="act_mode"]').value = rec.act_mode ?? '';
            document.querySelector('input[name="bank_charges"]').value = rec.bank_charges ?? 0;
            document.querySelector('input[name="ac_code"]').value = rec.ac_code || '';
            document.querySelector('input[name="cheque_no"]').value = rec.cheque_no || '';
            document.querySelector('input[name="cheque_date"]').value = rec.cheque_date || '';
            document.querySelector('input[name="gain_loss"]').value = rec.gain_loss ?? 0;
            document.querySelector('input[name="total_amount"]').value = rec.total_amount ?? 0;
            document.querySelector('input[name="total_amount_1"]').value = rec.total_amount_1 ?? 0;
            document.querySelector('input[name="approved_by"]').value = rec.approved_by || '';
            document.querySelector('input[name="approved_on"]').value = rec.approved_on || '';

            const agentSelect = document.querySelector('select[name="agent"]');
            if (agentSelect) updateAgentName(agentSelect);
            document.getElementById('ac_code_display').value = rec.ac_code || '';

            recalcTotal();

            const form = document.getElementById('header-form');
            form.action = `/agent-receipt-payments/${rec.id}`;
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

            updateCounters(data.current, data.total);

            navData.total = data.total;
            navData.current = data.current;
            navData.firstId = data.firstId;
            navData.lastId = data.lastId;
            navData.prevId = data.prevId;
            navData.nextId = data.nextId;

            updateNavButtons();

            window.history.replaceState({}, '', `/agent-receipt-payments/${rec.id}/edit`);
        }

        function toggleApprove(checkbox) {
            if (!arpId) {
                checkbox.checked = false;
                return;
            }

            const approve = checkbox.checked;
            const message = approve ? 'Approve this record?' : 'Revoke approval for this record?';

            window.Alerts.confirm({
                title: approve ? 'Approve record' : 'Revoke approval',
                text: message,
                confirmText: approve ? 'Yes, approve' : 'Yes, revoke',
                confirmColor: approve ? '#059669' : '#dc2626',
            }).then((confirmed) => {
                if (!confirmed) {
                    checkbox.checked = !approve;
                    return;
                }

                const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
                fetch(`/agent-receipt-payments/${arpId}/approve`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token,
                    },
                    body: JSON.stringify({ approved: approve }),
                }).then(resp => {
                    if (resp.ok) {
                        window.location.reload();
                    } else {
                        checkbox.checked = !approve;
                        window.Alerts.error('Approval failed.');
                    }
                }).catch(() => {
                    checkbox.checked = !approve;
                    window.Alerts.error('Network error.');
                });
            });
        }

        function updateCounters(current, total) {
            document.getElementById('nav-current-2').textContent = current;
            document.getElementById('nav-total-2').textContent = total;
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
