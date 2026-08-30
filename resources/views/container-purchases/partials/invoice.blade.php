<div class="rounded bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
    <h2 class="text-lg font-semibold text-topbar-text mb-6">Invoice Detail</h2>
    {!! html()->form('POST', '#')->id('cp-invoice-form')->class('space-y-5')->attribute('data-submit-url', route('container-purchases.invoices.store'))->attribute('data-store-url', route('container-purchases.invoices.store'))->attribute('data-child-table', 'cp-invoice-table')->open() !!}

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Transaction No.', 'invoice_trans_no_select')->class($labelClasses) !!}
                <input type="hidden" name="container_purchase_detail_id" id="invoice_container_purchase_detail_id" value="{{ old('container_purchase_detail_id', $containerPurchase?->id) }}">
                <select id="invoice_trans_no_select" class="cp-transno-select {{ $inputClasses }}" data-url="{{ route('container-purchases.transactions-for-purchase') }}"></select>
            </div>
            <div>
                {!! html()->label('Doc No.', 'invoice_doc_no')->class($labelClasses) !!}
                {!! html()->text('doc_no', null)->id('invoice_doc_no')->class($inputClasses) !!}
            </div>
            <div>
                {!! html()->label('Invoice No.', 'invoice_no_field')->class($labelClasses) !!}
                {!! html()->text('invoice_no', null)->id('invoice_no_field')->class($inputClasses) !!}
            </div>
            <div>
                {!! html()->label('Invoice Date', 'invoice_date')->class($labelClasses) !!}
                {!! html()->date('invoice_date', null)->id('invoice_date')->class($inputClasses) !!}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Settlement Type', 'invoice_settlement_type_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="settlement_type_id" id="invoice_settlement_type_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select settlement type</option>
                        @foreach ($settlementTypes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Payment Agent', 'invoice_payment_agent_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="payment_agent_id" id="invoice_payment_agent_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select agent</option>
                        @foreach ($agents as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Currency', 'invoice_currency')->class($labelClasses) !!}
                <div class="relative">
                    <select name="currency" id="invoice_currency" class="cp-currency-select {{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select currency</option>
                        @foreach ($currencies as $code => $label)
                            <option value="{{ $code }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Amount', 'invoice_amount')->class($labelClasses) !!}
                {!! html()->number('amount', null)->id('invoice_amount')->class($inputClasses)->attribute('step', 'any') !!}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Supplier', 'invoice_supplier_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="supplier_id" id="invoice_supplier_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select supplier</option>
                        @foreach ($suppliers as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Location', 'invoice_location_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="location_id" id="invoice_location_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select location</option>
                        @foreach ($ports as $id => $label)
                            <option value="{{ $id }}">{{ $label }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Sub Company', 'invoice_sub_company_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="sub_company_id" id="invoice_sub_company_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select sub company</option>
                        @foreach ($subCompanies as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Exchange Rate', 'invoice_exchange_rate')->class($labelClasses) !!}
                {!! html()->number('currency_exchange_rate', null)->id('invoice_exchange_rate')->class($inputClasses.' cp-rate-input')->attribute('step', 'any') !!}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Currency Code', 'invoice_currency_code')->class($labelClasses) !!}
                {!! html()->text('currency_code', null)->id('invoice_currency_code')->class($inputClasses.' cp-currency-code-input') !!}
            </div>
            <div>
                {!! html()->label('Total Amount', 'invoice_total_amount')->class($labelClasses) !!}
                {!! html()->number('total_amount', null)->id('invoice_total_amount')->class($inputClasses)->attribute('step', 'any') !!}
            </div>
            <div></div>
            <div></div>
        </div>

    {!! html()->form()->close() !!}

    <div class="mt-6 flex items-center justify-end gap-3">
        <button type="button" class="cancel-edit-btn hidden rounded-lg bg-gray-200 px-4 py-1.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-300">Cancel</button>
        <button type="submit" form="cp-invoice-form" class="{{ $submitClasses }}">Save</button>
    </div>

    <div class="mt-6">
        {!! $childTable->table() !!}
    </div>
</div>