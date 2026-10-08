<x-app-layout :title="'Sales Invoice'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-3 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500';
        $currencyCode = old('currency_code', $invoice->currency_code ?? 'PKR');
        $details = old('details', $invoice->details->isNotEmpty()
            ? $invoice->details->map(fn ($detail) => $detail->only(['description', 'container_number', 'amount']))->all()
            : [['description' => '', 'container_number' => '', 'amount' => '']]);
        if (! is_array($details) || $details === []) {
            $details = [['description' => '', 'container_number' => '', 'amount' => '']];
        }
    @endphp

    <div class="mx-auto max-w-full space-y-6" id="sales-invoice-page" data-currency-code="{{ $currencyCode }}">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.receipt', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Sales Invoice</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Create and manage customer sales invoices.</p>
                </div>
            </div>
            @if (! $isNew)
                <div class="flex items-center gap-3">
                    <a href="{{ route('sales-invoices.pdf-view', $invoice) }}" target="_blank" rel="noopener" class="rounded-lg bg-white px-4 py-2 text-sm font-semibold text-primary-900 ring-1 ring-inset ring-card-border hover:bg-primary-50">View PDF</a>
                    <a href="{{ route('sales-invoices.pdf', $invoice) }}" class="rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">Download PDF</a>
                </div>
            @endif
        </div>

        <div class="rounded-2xl bg-card-bg p-5 shadow-sm ring-1 ring-card-border sm:p-7">
            {!! html()->form($isNew ? 'POST' : 'PATCH', $isNew ? route('sales-invoices.store') : route('sales-invoices.update', $invoice))->id('sales-invoice-form')->class('space-y-6')->open() !!}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        {!! html()->label('Currency', 'currency_code')->class($labelClasses) !!}
                        <div class="relative">
                            <select id="currency_code" name="currency_code" class="{{ $inputClasses }} appearance-none pr-8" required>
                                @foreach ($currencyCodes as $code)
                                    <option value="{{ $code }}" @selected((string) old('currency_code', $invoice->currency_code ?? 'PKR') === $code)>{{ $code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">⌄</div>
                        </div>
                        @error('currency_code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        {!! html()->label('Status', 'status')->class($labelClasses) !!}
                        <div class="relative">
                            <select id="status" name="status" class="{{ $inputClasses }} appearance-none pr-8" required>
                                @foreach (config('dropdowns.sales_invoices.status') as $value => $label)
                                    <option value="{{ $value }}" @selected((string) old('status', $invoice->status ?? 'unpaid') === (string) $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">⌄</div>
                        </div>
                        @error('status')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        {!! html()->label('Bank Account', 'bank_account_id')->class($labelClasses) !!}
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <select id="bank_account_id" name="bank_account_id" class="{{ $inputClasses }} appearance-none pr-8">
                                    <option value="">No bank account</option>
                                    @foreach ($bankAccounts as $bankAccount)
                                        <option value="{{ $bankAccount->id }}" @selected((string) old('bank_account_id', $invoice->bank_account_id) === (string) $bankAccount->id)>{{ $bankAccount->optionLabel() }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">⌄</div>
                            </div>
                            @if (auth()->user()->isSuperAdmin() || auth()->user()->can('bank_accounts.add'))
                                <button type="button" id="bank-account-quick-create-btn" data-modal-target="bank-account-quick-create-modal" title="Add bank account" aria-label="Add bank account" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-900 text-white shadow-sm transition-all hover:bg-primary-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                                </button>
                            @endif
                        </div>
                        @error('bank_account_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('Invoice Number', 'invoice_number')->class($labelClasses) !!}
                        <input id="invoice_number" class="{{ $inputClasses }} bg-gray-100" value="{{ $invoice->invoice_number ?: 'Assigned when saved' }}" disabled>
                    </div>
                    <div>
                        {!! html()->label('Customer', 'party_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select id="party_id" name="party_id" class="{{ $inputClasses }} appearance-none pr-8" required>
                                <option value="">Select a customer</option>
                                @foreach ($parties as $party)
                                    <option value="{{ $party->id }}" @selected((string) old('party_id', $invoice->party_id) === (string) $party->id)>{{ $party->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">⌄</div>
                        </div>
                        @error('party_id')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        {!! html()->label('Invoice Date', 'invoice_date')->class($labelClasses) !!}
                        {!! html()->date('invoice_date', old('invoice_date', $invoice->invoice_date?->format('Y-m-d')))->class($inputClasses) !!}
                        @error('invoice_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        {!! html()->label('Due Date', 'due_date')->class($labelClasses) !!}
                        {!! html()->date('due_date', old('due_date', $invoice->due_date?->format('Y-m-d')))->class($inputClasses) !!}
                        @error('due_date')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('Our Reference', 'our_reference')->class($labelClasses) !!}
                        {!! html()->text('our_reference', old('our_reference', $invoice->our_reference))->class($inputClasses) !!}
                        @error('our_reference')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        {!! html()->label('Invoice Contact', 'customer_contact')->class($labelClasses) !!}
                        {!! html()->text('customer_contact', old('customer_contact', $invoice->customer_contact))->class($inputClasses) !!}
                        @error('customer_contact')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        {!! html()->label('VAT Rate (%)', 'vat_rate')->class($labelClasses) !!}
                        <input id="vat_rate" name="vat_rate" type="number" min="0" max="100" step="0.01" value="{{ old('vat_rate', $invoice->vat_rate ?? '0.00') }}" class="{{ $inputClasses }}" required>
                        @error('vat_rate')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        {!! html()->label('Created Date', 'created_date')->class($labelClasses) !!}
                        <input id="created_date" class="{{ $inputClasses }} bg-gray-100" value="{{ $invoice->created_at?->format('d/m/Y') ?? now()->format('d/m/Y') }}" disabled>
                    </div>
                </div>

                <div>
                    {!! html()->label('Remarks', 'remarks')->class($labelClasses) !!}
                    {!! html()->textarea('remarks', old('remarks', $invoice->remarks))->class($inputClasses)->rows(2) !!}
                    @error('remarks')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <section class="space-y-4" aria-labelledby="details-heading">
                    <div class="flex items-center justify-between border-b border-card-border pb-3">
                        <h2 id="details-heading" class="text-lg font-semibold text-gray-800">Invoice Details</h2>
                        <button type="button" id="add-sales-invoice-detail" class="rounded-lg bg-primary-900 px-3 py-2 text-sm font-semibold text-white hover:bg-primary-700">Add Line</button>
                    </div>
                    @error('details')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                    <div id="sales-invoice-details" class="space-y-4">
                        @foreach ($details as $index => $detail)
                            <div class="sales-invoice-detail grid grid-cols-1 items-end gap-5 sm:grid-cols-2 lg:grid-cols-4">
                                <div>
                                    {!! html()->label('Description', 'details_'.$index.'_description')->class($labelClasses) !!}
                                    <textarea name="details[{{ $index }}][description]" id="details_{{ $index }}_description" class="{{ $inputClasses }}" rows="2" required>{{ $detail['description'] ?? '' }}</textarea>
                                    @error("details.{$index}.description")<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    {!! html()->label('Container Number', 'details_'.$index.'_container_number')->class($labelClasses) !!}
                                    <input name="details[{{ $index }}][container_number]" id="details_{{ $index }}_container_number" value="{{ $detail['container_number'] ?? '' }}" class="{{ $inputClasses }}">
                                    @error("details.{$index}.container_number")<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div>
                                    {!! html()->label('Amount ('.$currencyCode.')', 'details_'.$index.'_amount')->class($labelClasses.' js-amount-label') !!}
                                    <input name="details[{{ $index }}][amount]" id="details_{{ $index }}_amount" type="number" min="0" step="0.01" value="{{ $detail['amount'] ?? '' }}" class="{{ $inputClasses }}" required>
                                    @error("details.{$index}.amount")<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                                </div>
                                <div class="flex items-center gap-4">
                                    <span class="line-total min-w-24 text-sm font-medium text-topbar-text">{{ $currencyCode }} 0.00</span>
                                    <button type="button" class="remove-sales-invoice-detail rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <div class="ml-auto max-w-sm space-y-2 border-t border-card-border pt-4 text-sm">
                    <div class="flex justify-between"><span>Subtotal</span><span id="invoice-subtotal">{{ $currencyCode }} 0.00</span></div>
                    <div class="flex justify-between"><span>VAT (<span id="invoice-vat-rate">0</span>%)</span><span id="invoice-vat-amount">{{ $currencyCode }} 0.00</span></div>
                    <div class="flex justify-between text-base font-bold"><span id="invoice-total-label">Total {{ $currencyCode }}</span><span id="invoice-total">{{ $currencyCode }} 0.00</span></div>
                </div>

                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-card-border pt-5">
                    <a href="{{ route('sales-invoices.index') }}" class="rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted hover:text-topbar-text">Cancel</a>
                    <button type="submit" class="rounded-lg bg-primary-900 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700">{{ $isNew ? 'Create Invoice' : 'Save Changes' }}</button>
                </div>
            {!! html()->form()->close() !!}

            @unless ($isNew)
                <form method="POST" action="{{ route('sales-invoices.destroy', $invoice) }}" class="delete-form mt-4 text-right" data-confirm="Delete this sales invoice and its detail lines?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-lg px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50">Delete Invoice</button>
                </form>
            @endunless
        </div>
    </div>

    @if (auth()->user()->isSuperAdmin() || auth()->user()->can('bank_accounts.add'))
        @include('bank-accounts._quick-create-modal')
    @endif

    <template id="sales-invoice-detail-template">
        <div class="sales-invoice-detail grid grid-cols-1 items-end gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div><label class="block text-sm font-medium text-topbar-text">Description</label><textarea name="details[__INDEX__][description]" class="mt-1 block w-full rounded-md border-0 bg-card-bg px-3 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border" rows="2" required></textarea></div>
            <div><label class="block text-sm font-medium text-topbar-text">Container Number</label><input name="details[__INDEX__][container_number]" class="mt-1 block w-full rounded-md border-0 bg-card-bg px-3 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border"></div>
            <div><label class="block text-sm font-medium text-topbar-text js-amount-label">Amount ({{ $currencyCode }})</label><input name="details[__INDEX__][amount]" type="number" min="0" step="0.01" class="mt-1 block w-full rounded-md border-0 bg-card-bg px-3 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border" required></div>
            <div class="flex items-center gap-4"><span class="line-total min-w-24 text-sm font-medium text-topbar-text">{{ $currencyCode }} 0.00</span><button type="button" class="remove-sales-invoice-detail rounded-lg bg-red-50 px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-100">Remove</button></div>
        </div>
    </template>
</x-app-layout>
