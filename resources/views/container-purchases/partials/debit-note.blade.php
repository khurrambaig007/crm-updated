<div class="rounded bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
    <h2 class="text-lg font-semibold text-topbar-text mb-6">Debit Note Detail</h2>
    {!! html()->form('POST', '#')->id('cp-debit-form')->class('space-y-5')->attribute('data-submit-url', route('container-purchases.debits.store'))->attribute('data-store-url', route('container-purchases.debits.store'))->attribute('data-child-table', 'cp-debit-table')->open() !!}

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Transaction No.', 'debit_trans_no_select')->class($labelClasses) !!}
                <input type="hidden" name="container_purchase_detail_id" id="debit_container_purchase_detail_id" value="{{ old('container_purchase_detail_id', $containerPurchase?->id) }}">
                <select id="debit_trans_no_select" class="cp-transno-select {{ $inputClasses }}" data-url="{{ route('container-purchases.transactions-for-purchase') }}"></select>
            </div>
            <div>
                {!! html()->label('Doc No.', 'debit_doc_no')->class($labelClasses) !!}
                {!! html()->text('doc_no', null)->id('debit_doc_no')->class($inputClasses) !!}
            </div>
            <div>
                {!! html()->label('Invoice', 'debit_invoice_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="invoice_id" id="debit_invoice_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select invoice</option>
                        @foreach ($invoices as $id => $invoiceNo)
                            <option value="{{ $id }}">{{ $invoiceNo }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Settlement Type', 'debit_settlement_type_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="settlement_type_id" id="debit_settlement_type_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select settlement type</option>
                        @foreach ($settlementTypes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Payment Agent', 'debit_payment_agent_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="payment_agent_id" id="debit_payment_agent_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select agent</option>
                        @foreach ($agents as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Currency', 'debit_currency')->class($labelClasses) !!}
                <div class="relative">
                    <select name="currency" id="debit_currency" class="cp-currency-select {{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select currency</option>
                        @foreach ($currencies as $code => $label)
                            <option value="{{ $code }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Amount', 'debit_amount')->class($labelClasses) !!}
                {!! html()->number('amount', null)->id('debit_amount')->class($inputClasses)->attribute('step', 'any') !!}
            </div>
            <div>
                {!! html()->label('Supplier', 'debit_supplier_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="supplier_id" id="debit_supplier_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select supplier</option>
                        @foreach ($suppliers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Location', 'debit_location_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="location_id" id="debit_location_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select location</option>
                        @foreach ($ports as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Sub Company', 'debit_sub_company_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="sub_company_id" id="debit_sub_company_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select sub company</option>
                        @foreach ($subCompanies as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Exchange Rate', 'debit_exchange_rate')->class($labelClasses) !!}
                {!! html()->number('currency_exchange_rate', null)->id('debit_exchange_rate')->class($inputClasses.' cp-rate-input')->attribute('step', 'any') !!}
            </div>
            <div>
                {!! html()->label('Currency Code', 'debit_currency_code')->class($labelClasses) !!}
                {!! html()->text('currency_code', null)->id('debit_currency_code')->class($inputClasses.' cp-currency-code-input') !!}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Total Amount', 'debit_total_amount')->class($labelClasses) !!}
                {!! html()->number('total_amount', null)->id('debit_total_amount')->class($inputClasses)->attribute('step', 'any') !!}
            </div>
            <div></div>
            <div></div>
            <div></div>
        </div>

    {!! html()->form()->close() !!}

    <div class="mt-6 flex items-center justify-end gap-3">
        <button type="button" class="cancel-edit-btn hidden rounded-lg bg-gray-200 px-4 py-1.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-300">Cancel</button>
        <button type="submit" form="cp-debit-form" class="{{ $submitClasses }}">Save</button>
    </div>

    <div class="mt-6">
        {!! $childTable->table() !!}
    </div>
</div>