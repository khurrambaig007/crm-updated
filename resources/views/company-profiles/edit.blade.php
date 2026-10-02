<x-app-layout :title="'Company Profile'">
    @php
        $isNew = ! $profile->exists;
        $logoUrl = $profile->logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($profile->logo) : null;

        // Always render at least one repeatable row so a "required" validation error
        // lands on a visible input instead of an absent one.
        $emailValues = old('emails', $profile->emails ?: ['']);
        if (! is_array($emailValues) || $emailValues === []) {
            $emailValues = [''];
        }

        $customFieldValues = old('custom_fields', $profile->custom_fields ?: []);
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
                @include('components.icons.building', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Company Profile</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Company name, logo and contact details shown across your workspace.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form($isNew ? 'POST' : 'PATCH', $isNew ? route('company-profile.store') : route('company-profile.update'))->id('company-profile-form')->acceptsFiles()->class('space-y-5')->open() !!}

                {{-- Company --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('Company Name', 'name')->class($labelClasses) !!}
                        {!! html()->text('name', old('name', $profile->name))->class($inputClasses)->required() !!}
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Logo', 'logo')->class($labelClasses) !!}
                        {!! html()->file('logo')->class($inputClasses)->acceptImage() !!}
                        <p class="mt-1.5 text-xs text-topbar-muted">JPG, PNG or WEBP. Max 2 MB.</p>
                        <div id="logo-preview-wrap" class="mt-2 flex items-center gap-3 {{ $logoUrl ? '' : 'hidden' }}">
                            <img id="logo-preview" src="{{ $logoUrl }}" alt="{{ $profile->name }} logo" class="h-12 w-12 rounded-lg object-contain ring-1 ring-card-border">
                            <label class="flex items-center gap-2 text-xs text-topbar-muted">
                                {!! html()->checkbox('remove_logo')->value('1')->checked(old('remove_logo')) !!}
                                Remove
                            </label>
                        </div>
                        @error('logo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Website', 'website')->class($labelClasses) !!}
                        {!! html()->text('website', old('website', $profile->website))->class($inputClasses)->placeholder('https://') !!}
                        @error('website')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('Number', 'number')->class($labelClasses) !!}
                        {!! html()->text('number', old('number', $profile->number))->class($inputClasses) !!}
                        @error('number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('Subtitle', 'subtitle')->class($labelClasses) !!}
                        {!! html()->text('subtitle', old('subtitle', $profile->subtitle))->class($inputClasses)->placeholder('e.g. Shipping Line') !!}
                        <p class="mt-1.5 text-xs text-topbar-muted">Shown under the company name in the sidebar and on the login screen.</p>
                        @error('subtitle')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('&nbsp;', 'subtitle_placeholder')->class($labelClasses) !!}
                    </div>

                    <div>
                        {!! html()->label('&nbsp;', 'subtitle_placeholder_2')->class($labelClasses) !!}
                    </div>

                    <div>
                        {!! html()->label('&nbsp;', 'subtitle_placeholder_3')->class($labelClasses) !!}
                    </div>
                </div>

                {{-- Emails --}}
                <div class="border-t border-card-border py-6">
                    <h3 class="text-xl font-semibold text-gray-800">Email</h3>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div class="mb-3 flex items-center justify-end">
                        <button type="button" id="add-email" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition-all hover:bg-emerald-500 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M5 12h14" /><path d="M12 5v14" /></svg>
                            ADD EMAIL
                        </button>
                    </div>
                </div>

                <div id="emails-container" class="space-y-5">
                    @foreach ($emailValues as $index => $email)
                        <div class="dyn-row grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Email', 'emails_'.$index)->class($labelClasses) !!}
                                <input type="email" name="emails[{{ $index }}]" id="emails_{{ $index }}" value="{{ $email }}" placeholder="name@company.com" class="{{ $inputClasses }}">
                                @error("emails.{$index}")
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div></div>
                            <div class="flex items-end">
                                <button type="button" class="remove-row inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:text-white" title="Remove">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                                </button>
                            </div>
                            <div></div>
                        </div>
                    @endforeach
                </div>
                @error('emails')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror

                {{-- Person in contact --}}
                <div class="border-t border-card-border py-6">
                    <h3 class="text-xl font-semibold text-gray-800">Person in Contact</h3>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('PIC Name', 'pic_name')->class($labelClasses) !!}
                        {!! html()->text('pic_name', old('pic_name', $profile->pic_name))->class($inputClasses) !!}
                        @error('pic_name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('PIC Email', 'pic_email')->class($labelClasses) !!}
                        {!! html()->email('pic_email', old('pic_email', $profile->pic_email))->class($inputClasses) !!}
                        @error('pic_email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        {!! html()->label('PIC Number', 'pic_number')->class($labelClasses) !!}
                        {!! html()->text('pic_number', old('pic_number', $profile->pic_number))->class($inputClasses) !!}
                        @error('pic_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div></div>
                </div>

                {{-- Dynamic fields --}}
                <div id="add-field-container" class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div></div>
                    <div></div>
                    <div></div>
                    <div class="mb-3 flex items-center justify-end">
                        <button type="button" id="add-field" class="inline-flex items-center gap-1 rounded-lg bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-600 transition-all hover:bg-emerald-500 hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5"><path d="M5 12h14" /><path d="M12 5v14" /></svg>
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
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>
                                </button>
                            </div>
                            <div></div>
                        </div>
                    @endforeach
                </div>

                {{-- Message --}}
                <div class="border-t border-card-border py-6">
                    <h3 class="text-xl font-semibold text-gray-800">Message</h3>
                </div>

                <div>
                    {!! html()->textarea('message', old('message', $profile->message))->class($inputClasses)->rows(5) !!}
                    @error('message')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    {!! html()->submit($isNew ? 'Create profile' : 'Save changes')->class($submitClasses) !!}
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
                + '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg>'
                + '</button>';
        }

        function label(text, forId) {
            return $('<label>').addClass(labelClasses).attr('for', forId).text(text);
        }

        $('#add-email').on('click', function () {
            var container = document.getElementById('emails-container');
            var index = $(container).find('.dyn-row').length;

            var $row = $('<div>').addClass('dyn-row grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4');
            var $first = $('<div>').append(label('Email', 'emails_' + index));
            $first.append(
                $('<input>').attr({ type: 'email', name: 'emails[' + index + ']', id: 'emails_' + index })
                    .addClass(inputClasses).attr('placeholder', 'name@company.com')
            );
            var $remove = $('<div>').addClass('flex items-end').html(trashButton());

            $row.append($first, $('<div>'), $remove, $('<div>'));
            $(container).append($row);
        });

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

        $('#company-profile-form').on('click', '.remove-row', function () {
            var $row = $(this).closest('.dyn-row');
            var container = $row.parent().attr('id');
            $row.remove();

            if (container === 'emails-container') {
                reindexRows(container, 'emails');
            } else if (container === 'custom-fields-container') {
                reindexRows(container, 'custom_fields');
            }
        });

        // Live logo preview, so the user can confirm the right file before saving.
        $('input[name="logo"]').on('change', function () {
            var file = this.files && this.files[0];
            if (!file || !/^image\//.test(file.type)) {
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                $('#logo-preview').attr('src', event.target.result);
                $('#logo-preview-wrap').removeClass('hidden');
            };
            reader.readAsDataURL(file);
        });
    });
</script>
@endpush
