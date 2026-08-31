<div class="rounded bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
    <h2 class="text-lg font-semibold text-topbar-text mb-6">PO Cancel Detail</h2>
    {!! html()->form('POST', '#')->id('cp-po-cancel-form')->class('space-y-5')->attribute('data-submit-url', route('container-purchases.po-cancels.store'))->attribute('data-store-url', route('container-purchases.po-cancels.store'))->attribute('data-child-table', 'cp-po-cancel-table')->open() !!}

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Transaction No.', 'po_cancel_trans_no_select')->class($labelClasses) !!}
                <input type="hidden" name="container_purchase_detail_id" id="po_cancel_container_purchase_detail_id" value="{{ old('container_purchase_detail_id', $containerPurchase?->id) }}">
                <select id="po_cancel_trans_no_select" class="cp-transno-select {{ $inputClasses }}" data-url="{{ route('container-purchases.transactions-for-purchase') }}"></select>
            </div>
            <div>
                {!! html()->label('Invoice No.', 'po_cancel_invoice_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="invoice_id" id="po_cancel_invoice_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select invoice</option>
                        @foreach ($invoices as $id => $invoiceNo)
                            <option value="{{ $id }}">{{ $invoiceNo }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Transaction Date', 'po_cancel_transaction_date')->class($labelClasses) !!}
                {!! html()->date('transaction_date', null)->id('po_cancel_transaction_date')->class($inputClasses) !!}
            </div>
            <div></div>
        </div>

    {!! html()->form()->close() !!}

    <div class="mt-6 flex items-center justify-end gap-3">
        <button type="button" class="cancel-edit-btn hidden rounded-lg bg-gray-200 px-4 py-1.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-300">Cancel</button>
        <button type="submit" form="cp-po-cancel-form" class="{{ $submitClasses }}">Save</button>
    </div>

    <div class="mt-6">
        {!! $childTable->table() !!}
    </div>
</div>