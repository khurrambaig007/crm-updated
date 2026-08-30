<div class="rounded bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
    <h2 class="text-lg font-semibold text-topbar-text mb-6">Purchase Detail</h2>
    {!! html()->form('POST', '#')->id('cp-purchase-form')->class('space-y-5')->attribute('data-submit-url', route('container-purchases.models.store'))->attribute('data-store-url', route('container-purchases.models.store'))->attribute('data-child-table', 'cp-purchase-table')->open() !!}

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Transaction No.', 'purchase_trans_no_select')->class($labelClasses) !!}
                <input type="hidden" name="container_purchase_detail_id" id="purchase_container_purchase_detail_id" value="{{ old('container_purchase_detail_id', $containerPurchase?->id) }}">
                <select id="purchase_trans_no_select" class="cp-transno-select {{ $inputClasses }}" data-url="{{ route('container-purchases.transactions-for-purchase') }}"></select>
            </div>
            <div>
                {!! html()->label('Container Size', 'purchase_container_size_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="container_size_id" id="purchase_container_size_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select size</option>
                        @foreach ($containerSizes as $id => $size)
                            <option value="{{ $id }}">{{ $size }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Container Type', 'purchase_container_type_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="container_type_id" id="purchase_container_type_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select type</option>
                        @foreach ($containerTypes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Container Kind', 'purchase_container_kind_id')->class($labelClasses) !!}
                <div class="relative">
                    <select name="container_kind_id" id="purchase_container_kind_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select kind</option>
                        @foreach ($containerKinds as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Quantity', 'purchase_quantity')->class($labelClasses) !!}
                {!! html()->number('quantity', null)->id('purchase_quantity')->class($inputClasses)->attribute('step', 'any') !!}
            </div>
            <div>
                {!! html()->label('Amount', 'purchase_amount')->class($labelClasses) !!}
                {!! html()->number('amount', null)->id('purchase_amount')->class($inputClasses)->attribute('step', 'any') !!}
            </div>
            <div>
                {!! html()->label('Rate', 'purchase_rate')->class($labelClasses) !!}
                {!! html()->number('rate', null)->id('purchase_rate')->class($inputClasses)->attribute('step', 'any') !!}
            </div>
            <div></div>
        </div>

    {!! html()->form()->close() !!}

    <div class="mt-6 flex items-center justify-end gap-3">
        <button type="button" class="cancel-edit-btn hidden rounded-lg bg-gray-200 px-4 py-1.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-300">Cancel</button>
        <button type="submit" form="cp-purchase-form" class="{{ $submitClasses }}">Save</button>
    </div>

    <div class="mt-6">
        {!! $childTable->table() !!}
    </div>
</div>