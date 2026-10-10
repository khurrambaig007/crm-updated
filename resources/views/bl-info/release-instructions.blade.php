<x-app-layout :title="$tabTitle">
    @php
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full appearance-none rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition cursor-pointer';
        $textareaClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $chevron = '<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg></div>';
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => 'Bill of lading header for booking'])

        @include('bl-info._nav')

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    @include('components.icons.key', ['classes' => 'h-5 w-5'])
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-topbar-text">Release Instructions</h2>
                    <p class="text-xs text-topbar-muted">Release authorization details for this bill of lading</p>
                </div>
            </div>

            {!! html()->form('PATCH', route('bl-info.release-instructions.update', $booking))->id('release-instructions-form')->class('space-y-5')->open() !!}

            {{-- Row 1: Date · Status · Type · Empty --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Date <span class="text-red-500">*</span>', 'release_instruction_date')->class($labelClasses) !!}
                    {!! html()->date('release_instruction_date', old('release_instruction_date', $blDetail->release_instruction_date))->class($inputClasses)->required() !!}
                    @error('release_instruction_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Status', 'release_instruction_status')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="release_instruction_status" id="release_instruction_status" class="{{ $selectClasses }}">
                            <option value="">Select status</option>
                            @foreach ($releaseStatuses as $value => $label)
                                <option value="{{ $value }}" {{ old('release_instruction_status', $blDetail->release_instruction_status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('release_instruction_status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Type', 'release_instruction_type')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="release_instruction_type" id="release_instruction_type" class="{{ $selectClasses }}">
                            <option value="">Select type</option>
                            @foreach ($releaseTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('release_instruction_type', $blDetail->release_instruction_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('release_instruction_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('&nbsp;', 'release_instruction_empty')->class($labelClasses) !!}
                </div>
            </div>

            {{-- Row 2: Description --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-3">
                    {!! html()->label('Description', 'release_instruction_description')->class($labelClasses) !!}
                    {!! html()->textarea('release_instruction_description', old('release_instruction_description', $blDetail->release_instruction_description))->class($textareaClasses)->rows(4)->placeholder('Enter release instructions description...') !!}
                    @error('release_instruction_description')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    {!! html()->label('&nbsp;', 'release_instruction_empty_2')->class($labelClasses) !!}
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">Cancel</a>
                {!! html()->submit('Save Release Instructions')->class($submitClasses) !!}
            </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>