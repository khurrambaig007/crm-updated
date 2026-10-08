<x-app-layout :title="'BL Info'">
    @php
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full appearance-none rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition cursor-pointer';
        $lockedClasses = 'mt-1 block w-full rounded-md border-0 bg-gray-100 px-2.5 py-1.5 text-sm text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $checkboxClasses = 'h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $chevron = '<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg></div>';
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => 'Bill of lading header for booking'])

        @include('bl-info._nav')

        <div class="rounded bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                @include('components.icons.package', ['classes' => 'h-5 w-5 text-primary-600'])
                <h2 class="text-lg font-semibold text-topbar-text">Bl Info</h2>
            </div>

            {!! html()->form('PATCH', route('bl-info.update', $booking))->id('bl-info-form')->class('space-y-5')->open() !!}

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('BL Date <span class="text-red-500">*</span>', 'bl_info_date')->class($labelClasses) !!}
                    {!! html()->date('bl_info_date', old('bl_info_date', $blDetail->bl_info_date))->class($inputClasses)->required() !!}
                    @error('bl_info_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Accounting Date <span class="text-red-500">*</span>', 'bl_info_accounting_date')->class($labelClasses) !!}
                    {!! html()->date('bl_info_accounting_date', old('bl_info_accounting_date', $blDetail->bl_info_accounting_date))->class($inputClasses)->required()->attribute('min', $blDetail->bl_info_date) !!}
                    @error('bl_info_accounting_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Carrier MBL No.', 'bl_info_carrier_mbl_no')->class($labelClasses) !!}
                    {!! html()->text('bl_info_carrier_mbl_no', old('bl_info_carrier_mbl_no', $blDetail->bl_info_carrier_mbl_no))->class($inputClasses) !!}
                    @error('bl_info_carrier_mbl_no')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('BL#', 'bl_info_bl_number')->class($labelClasses) !!}
                    <div class="relative">
                        {!! html()->text('bl_info_bl_number', $blDetail->bl_info_bl_number)->class($lockedClasses . ' pr-8')->attribute('readonly', 'readonly') !!}
                        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted" title="Auto-generated">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Agent', 'bl_info_agent')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_info_agent" id="bl_info_agent" class="{{ $selectClasses }}">
                            <option value="">Select agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('bl_info_agent', $blDetail->bl_info_agent) == $agent->id ? 'selected' : '' }}>{{ $agent->code ? '('.$agent->code.') '.$agent->name : $agent->name }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_info_agent')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Agent Name', 'bl_info_agent_detail')->class($labelClasses) !!}
                    <input type="text" id="bl_info_agent_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from agent" disabled>
                </div>

                <div>
                    {!! html()->label('Vessel / Voyage', 'bl_info_vessel_voyage_1')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_info_vessel_voyage_1" id="bl_info_vessel_voyage_1" class="{{ $selectClasses }}">
                            <option value="">Select vessel / voyage</option>
                            @foreach ($vesselVoyages as $vesselVoyage)
                                <option value="{{ $vesselVoyage->id }}" data-detail="{{ $vesselVoyage->voyage_number }}" {{ old('bl_info_vessel_voyage_1', $blDetail->bl_info_vessel_voyage_1) == $vesselVoyage->id ? 'selected' : '' }}>{{ $vesselVoyage->vessel_name }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_info_vessel_voyage_1')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Voyage No.', 'bl_info_vessel_voyage_detail')->class($labelClasses) !!}
                    <input type="text" id="bl_info_vessel_voyage_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from voyage" disabled>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Booking Number', 'bl_info_booking_no')->class($labelClasses) !!}
                    {!! html()->text('bl_info_booking_no', $blDetail->bl_info_booking_no)->class($lockedClasses)->attribute('readonly', 'readonly') !!}
                </div>

                <div>
                    {!! html()->label('Sailing Date', 'bl_info_sailing_date')->class($labelClasses) !!}
                    {!! html()->date('bl_info_sailing_date', $blDetail->bl_info_sailing_date)->class($lockedClasses)->attribute('readonly', 'readonly') !!}
                </div>

                <div>
                    {!! html()->label('Booking SL Status', 'bl_info_booking_si_status')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_info_booking_si_status" id="bl_info_booking_si_status" class="{{ $selectClasses }}">
                            <option value="">Select status</option>
                            @foreach ($siStatuses as $value => $label)
                                <option value="{{ $value }}" {{ old('bl_info_booking_si_status', $blDetail->bl_info_booking_si_status) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_info_booking_si_status')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-end">
                    <label for="bl_info_transshipment" class="flex cursor-pointer items-center gap-2 rounded-md px-1 py-1.5 text-sm font-medium text-topbar-text">
                        {{-- Hidden fallback so un-ticking the box still submits a false value. --}}
                        <input type="hidden" name="bl_info_transshipment" value="0">
                        {!! html()->checkbox('bl_info_transshipment', old('bl_info_transshipment', $blDetail->bl_info_transshipment))->id('bl_info_transshipment')->class($checkboxClasses) !!}
                        Allow Without Container No.
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">Cancel</a>
                {!! html()->submit('Save BL Info')->class($submitClasses) !!}
            </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>
