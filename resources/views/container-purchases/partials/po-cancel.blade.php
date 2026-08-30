<div class="rounded bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
    <h2 class="text-lg font-semibold text-topbar-text mb-6">PO Cancel Detail</h2>
    {!! html()->form('POST', '#')->id('cp-po-cancel-form')->class('space-y-5')->attribute('data-submit-url', route('container-purchases.po-cancels.store'))->attribute('data-store-url', route('container-purchases.po-cancels.store'))->attribute('data-child-table', 'cp-po-cancel-table')->open() !!}

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Doc No.', 'po_cancel_doc_no')->class($labelClasses) !!}
                {!! html()->text('doc_no', null)->id('po_cancel_doc_no')->class($inputClasses) !!}
            </div>
            <div>
                {!! html()->label('Transaction Date', 'po_cancel_transaction_date')->class($labelClasses) !!}
                {!! html()->date('transaction_date', null)->id('po_cancel_transaction_date')->class($inputClasses) !!}
            </div>
            <div>
                {!! html()->label('Trans No.', 'po_cancel_trans_no')->class($labelClasses) !!}
                {!! html()->text('trans_no', null)->id('po_cancel_trans_no')->class($inputClasses) !!}
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