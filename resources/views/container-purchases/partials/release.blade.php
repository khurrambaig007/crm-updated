<div class="rounded bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
    <h2 class="text-lg font-semibold text-topbar-text mb-6">Release Detail</h2>
    {!! html()->form('POST', '#')->id('cp-release-form')->class('space-y-5')->attribute('data-submit-url', route('container-purchases.releases.store'))->attribute('data-store-url', route('container-purchases.releases.store'))->attribute('data-child-table', 'cp-release-table')->open() !!}

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Transaction No.', 'release_trans_no_select')->class($labelClasses) !!}
                <input type="hidden" name="container_purchase_detail_id" id="release_container_purchase_detail_id" value="{{ old('container_purchase_detail_id', $containerPurchase?->id) }}">
                <select id="release_trans_no_select" class="cp-transno-select {{ $inputClasses }}" data-url="{{ route('container-purchases.transactions-for-purchase') }}"></select>
            </div>
            <div>
                {!! html()->label('Container No.', 'release_container_number')->class($labelClasses) !!}
                {!! html()->text('container_number', null)->id('release_container_number')->class($inputClasses) !!}
            </div>
            <div>
                {!! html()->label('Container Size', 'release_container_size')->class($labelClasses) !!}
                <div class="relative">
                    <select name="container_size" id="release_container_size" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select size</option>
                        @foreach ($containerSizes as $id => $size)
                            <option value="{{ $id }}">{{ $size }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('Container Type', 'release_container_type')->class($labelClasses) !!}
                <div class="relative">
                    <select name="container_type" id="release_container_type" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select type</option>
                        @foreach ($containerTypes as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Container Kind', 'release_container_kind')->class($labelClasses) !!}
                <div class="relative">
                    <select name="container_kind" id="release_container_kind" class="{{ $selectClasses }} appearance-none cursor-pointer">
                        <option value="">Select kind</option>
                        @foreach ($containerKinds as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                    {!! $chevron !!}
                </div>
            </div>
            <div>
                {!! html()->label('M/F Year', 'release_m_f_year')->class($labelClasses) !!}
                {!! html()->text('m_f_year', null)->id('release_m_f_year')->class($inputClasses) !!}
            </div>
            <div>
                {!! html()->label('Rate', 'release_rate')->class($labelClasses) !!}
                {!! html()->number('rate', null)->id('release_rate')->class($inputClasses)->attribute('step', 'any') !!}
            </div>
            <div>
                {!! html()->label('Remarks', 'release_remarks')->class($labelClasses) !!}
                {!! html()->textarea('remarks', null)->id('release_remarks')->class($inputClasses)->rows(1) !!}
            </div>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Original Container No.', 'release_original_container_number')->class($labelClasses) !!}
                {!! html()->text('original_container_number', null)->id('release_original_container_number')->class($inputClasses) !!}
            </div>
            <div></div>
            <div></div>
            <div></div>
        </div>

    {!! html()->form()->close() !!}

    <div class="mt-6 flex items-center justify-end gap-3">
        <button type="button" class="cancel-edit-btn hidden rounded-lg bg-gray-200 px-4 py-1.5 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-300">Cancel</button>
        <button type="submit" form="cp-release-form" class="{{ $submitClasses }}">Save</button>
    </div>

    <div class="mt-6">
        {!! $childTable->table() !!}
    </div>
</div>