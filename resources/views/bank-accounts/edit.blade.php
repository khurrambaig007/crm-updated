<x-app-layout :title="'Bank Account'">
    @php
        $isNew = ! $account->exists;

        // Always render at least one repeatable row so a validation error can land on
        // a visible input instead of an absent one.
        $customFieldValues = old('custom_fields', $account->custom_fields ?: []);
        if (! is_array($customFieldValues) || $customFieldValues === []) {
            $customFieldValues = [['key' => '', 'value' => '']];
        }

        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
    @endphp

    <div class="mx-auto max-w-full space-y-8">
        <div>
            <div class="flex items-center gap-3">
                @include('components.icons.landmark', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Bank Account</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Bank and beneficiary details used across the system.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form($isNew ? 'POST' : 'PATCH', $isNew ? route('bank-accounts.store') : route('bank-accounts.update'))->id('bank-account-form')->class('space-y-5')->open() !!}

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('Bank', 'bank')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="bank" id="bank" class="{{ $selectClasses }}">
                                <option value="">Select bank</option>
                                @foreach (config('dropdowns.bank_accounts.bank', []) as $value => $label)
                                    <option value="{{ $value }}" @selected((string) old('bank', $account->bank) === (string) $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                        @error('bank')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Beneficiary Name', 'beneficiary_name')->class($labelClasses) !!}
                        {!! html()->text('beneficiary_name', old('beneficiary_name', $account->beneficiary_name))->class($inputClasses)->required() !!}
                        @error('beneficiary_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Bank Name', 'bank_name')->class($labelClasses) !!}
                        {!! html()->text('bank_name', old('bank_name', $account->bank_name))->class($inputClasses) !!}
                        @error('bank_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Account', 'account')->class($labelClasses) !!}
                        {!! html()->text('account', old('account', $account->account))->class($inputClasses)->required() !!}
                        @error('account')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('IBAN', 'iban')->class($labelClasses) !!}
                        {{-- IBAN must stay verbatim: uppercase-lowering or trimming mid-string
                             would corrupt it, so no text-transform is applied here. --}}
                        {!! html()->text('iban', old('iban', $account->iban))->class($inputClasses) !!}
                        @error('iban')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('SWIFT', 'swift')->class($labelClasses) !!}
                        {!! html()->text('swift', old('swift', $account->swift))->class($inputClasses) !!}
                        @error('swift')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('&nbsp;', 'swift_placeholder')->class($labelClasses) !!}
                    </div>

                    <div>
                        {!! html()->label('&nbsp;', 'swift_placeholder_2')->class($labelClasses) !!}
                    </div>
                </div>

                <div class="border-t border-card-border py-6">
                    <h3 class="text-xl font-semibold text-gray-800">Address</h3>
                </div>

                <div>
                    {!! html()->textarea('address', old('address', $account->address))->class($inputClasses)->rows(3) !!}
                    @error('address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dynamic fields --}}
                <div class="border-t border-card-border py-6">
                    <h3 class="text-xl font-semibold text-gray-800">Additional Fields</h3>
                </div>

                <div id="add-field-container" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div class="mb-3 flex items-center justify-end">
                        <button type="button" id="add-field" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition-all hover:bg-emerald-500 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                            ADD FIELD
                        </button>
                    </div>
                </div>

                <div id="custom-fields-container" class="space-y-5">
                    @foreach ($customFieldValues as $index => $field)
                        <div class="dyn-row grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Label', 'custom_field_key_'.$index)->class($labelClasses) !!}
                                <input type="text" name="custom_fields[{{ $index }}][key]" id="custom_field_key_{{ $index }}" value="{{ $field['key'] ?? '' }}" placeholder="Label" class="{{ $inputClasses }}">
                                @error("custom_fields.{$index}.key")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                {!! html()->label('Value', 'custom_field_value_'.$index)->class($labelClasses) !!}
                                <input type="text" name="custom_fields[{{ $index }}][value]" id="custom_field_value_{{ $index }}" value="{{ $field['value'] ?? '' }}" placeholder="Value" class="{{ $inputClasses }}">
                                @error("custom_fields.{$index}.value")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove-row inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:text-white" title="Remove">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </button>
                            </div>
                            <div></div>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    {!! html()->submit($isNew ? 'Create bank account' : 'Save changes')->class($submitClasses) !!}
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    $(document).ready(function () {
        var inputClasses = @json($inputClasses);
        var labelClasses = @json($labelClasses);

        /**
         * Rewrite the numeric index of every repeatable input so the POST body
         * stays a dense, zero-based array after rows are removed.
         */
        function reindexRows(container, field) {
            $(container).find('.dyn-row').each(function (index) {
                $(this).find('[name^="' + field + '["]').each(function () {
                    this.name = this.name.replace(/\[\d+\]/, '[' + index + ']');
                });

                $(this).find('input').each(function () {
                    var id = $(this).attr('id');
                    if (id) {
                        $(this).attr('id', id.replace(/\d+/, index));
                    }
                });
            });
        }

        function trashButton() {
            return '<button type="button" class="remove-row inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:text-white" title="Remove">'
                + '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>'
                + '</button>';
        }

        function label(text, forId) {
            return $('<label>').addClass(labelClasses).attr('for', forId).text(text);
        }

        $('#add-field').on('click', function () {
            var container = document.getElementById('custom-fields-container');
            var index = $(container).find('.dyn-row').length;

            var $row = $('<div>').addClass('dyn-row grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4');
            var $key = $('<div>').append(label('Label', 'custom_field_key_' + index));
            $key.append(
                $('<input>').attr({ type: 'text', name: 'custom_fields[' + index + '][key]', id: 'custom_field_key_' + index })
                    .addClass(inputClasses).attr('placeholder', 'Label')
            );
            var $value = $('<div>').append(label('Value', 'custom_field_value_' + index));
            $value.append(
                $('<input>').attr({ type: 'text', name: 'custom_fields[' + index + '][value]', id: 'custom_field_value_' + index })
                    .addClass(inputClasses).attr('placeholder', 'Value')
            );
            var $remove = $('<div>').addClass('flex items-end').html(trashButton());

            $row.append($key, $value, $remove, $('<div>'));
            $(container).append($row);
        });

        $('#bank-account-form').on('click', '.remove-row', function () {
            var $row = $(this).closest('.dyn-row');
            var container = $row.parent().attr('id');
            $row.remove();

            if (container === 'custom-fields-container') {
                reindexRows(container, 'custom_fields');
            }
        });
    });
</script>
@endpush
