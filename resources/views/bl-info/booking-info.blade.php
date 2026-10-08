<x-app-layout :title="'Booking Info'">
    @php
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full appearance-none rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition cursor-pointer';
        $lockedClasses = 'mt-1 block w-full rounded-md border-0 bg-gray-100 px-2.5 py-1.5 text-sm text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $chevron = '<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg></div>';
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => 'Bill of lading header for booking'])

        @include('bl-info._nav')

        <div class="rounded bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                @include('components.icons.calendar', ['classes' => 'h-5 w-5 text-primary-600'])
                <h2 class="text-lg font-semibold text-topbar-text">Booking Info</h2>
            </div>

            {!! html()->form('PATCH', route('bl-info.booking-info.update', $booking))->id('booking-info-form')->class('space-y-5')->open() !!}

            {{-- Row 1: POL · Carrier · Reference --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('POL', 'booking_info_pol')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_pol" id="booking_info_pol" class="{{ $selectClasses }}">
                            <option value="">Select POL</option>
                            @foreach ($pols as $pol)
                                <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('booking_info_pol', $blDetail->booking_info_pol) == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_pol')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('POL Detail', 'booking_info_pol_detail')->class($labelClasses) !!}
                    <input type="text" id="booking_info_pol_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from port" disabled>
                </div>

                <div>
                    {!! html()->label('Carrier', 'booking_info_cntr_owner')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_cntr_owner" id="booking_info_cntr_owner" class="{{ $selectClasses }}">
                            <option value="">Select carrier</option>
                            @foreach ($carriers as $value => $label)
                                <option value="{{ $value }}" {{ old('booking_info_cntr_owner', $blDetail->booking_info_cntr_owner) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_cntr_owner')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Reference No.', 'booking_info_reference')->class($labelClasses) !!}
                    {!! html()->text('booking_info_reference', old('booking_info_reference', $blDetail->booking_info_reference))->class($inputClasses) !!}
                    @error('booking_info_reference')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Row 2: POFD · Agent --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('POFD', 'booking_info_pofd')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_pofd" id="booking_info_pofd" class="{{ $selectClasses }}">
                            <option value="">Select POFD</option>
                            @foreach ($pofds as $pod)
                                <option value="{{ $pod->id }}" data-detail="{{ $pod->city }}, {{ $pod->country }}" {{ old('booking_info_pofd', $blDetail->booking_info_pofd) == $pod->id ? 'selected' : '' }}>{{ $pod->location_code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_pofd')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('POFD Detail', 'booking_info_pofd_detail')->class($labelClasses) !!}
                    <input type="text" id="booking_info_pofd_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from port" disabled>
                </div>

                <div>
                    {!! html()->label('Agent Code', 'booking_info_agent_pofd')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_agent_pofd" id="booking_info_agent_pofd" class="{{ $selectClasses }}">
                            <option value="">Select agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('booking_info_agent_pofd', $blDetail->booking_info_agent_pofd) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_agent_pofd')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Agent Name', 'booking_info_agent_pofd_detail')->class($labelClasses) !!}
                    <input type="text" id="booking_info_agent_pofd_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from agent" disabled>
                </div>
            </div>

            {{-- Row 3: POT · Agent --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('POT', 'booking_info_pot_1')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_pot_1" id="booking_info_pot_1" class="{{ $selectClasses }}">
                            <option value="">Select POT</option>
                            @foreach ($pols as $pol)
                                <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('booking_info_pot_1', $blDetail->booking_info_pot_1) == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_pot_1')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('POT Detail', 'booking_info_pot_1_detail')->class($labelClasses) !!}
                    <input type="text" id="booking_info_pot_1_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from port" disabled>
                </div>

                <div>
                    {!! html()->label('Agent Code', 'booking_info_agent_1')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_agent_1" id="booking_info_agent_1" class="{{ $selectClasses }}">
                            <option value="">Select agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('booking_info_agent_1', $blDetail->booking_info_agent_1) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_agent_1')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Agent Name', 'booking_info_agent_1_detail')->class($labelClasses) !!}
                    <input type="text" id="booking_info_agent_1_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from agent" disabled>
                </div>
            </div>

            {{-- Row 4: POT 1 · Agent --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('POT 1', 'booking_info_pot_2')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_pot_2" id="booking_info_pot_2" class="{{ $selectClasses }}">
                            <option value="">Select POT 1</option>
                            @foreach ($pols as $pol)
                                <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('booking_info_pot_2', $blDetail->booking_info_pot_2) == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_pot_2')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('POT 1 Detail', 'booking_info_pot_2_detail')->class($labelClasses) !!}
                    <input type="text" id="booking_info_pot_2_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from port" disabled>
                </div>

                <div>
                    {!! html()->label('Agent Code', 'booking_info_agent_2')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_agent_2" id="booking_info_agent_2" class="{{ $selectClasses }}">
                            <option value="">Select agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('booking_info_agent_2', $blDetail->booking_info_agent_2) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_agent_2')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Agent Name', 'booking_info_agent_2_detail')->class($labelClasses) !!}
                    <input type="text" id="booking_info_agent_2_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from agent" disabled>
                </div>
            </div>

            {{-- Row 5: Shipper/BP · Consignee --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Shipper/BP', 'booking_info_shipper_bp')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_shipper_bp" id="booking_info_shipper_bp" class="{{ $selectClasses }}">
                            <option value="">Select Shipper/BP</option>
                            @foreach ($shipperBps as $shipperBp)
                                <option value="{{ $shipperBp->id }}" data-detail="{{ $shipperBp->name }}" {{ old('booking_info_shipper_bp', $blDetail->booking_info_shipper_bp) == $shipperBp->id ? 'selected' : '' }}>{{ $shipperBp->code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_shipper_bp')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Shipper/BP Detail', 'booking_info_shipper_bp_detail')->class($labelClasses) !!}
                    <input type="text" id="booking_info_shipper_bp_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from shipper" disabled>
                </div>

                <div>
                    {!! html()->label('Consignee', 'booking_info_consignee')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="booking_info_consignee" id="booking_info_consignee" class="{{ $selectClasses }}">
                            <option value="">Select consignee</option>
                            @foreach ($parties as $party)
                                <option value="{{ $party->id }}" data-detail="{{ $party->name }}" {{ old('booking_info_consignee', $blDetail->booking_info_consignee) == $party->id ? 'selected' : '' }}>{{ $party->code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('booking_info_consignee')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Consignee Detail', 'booking_info_consignee_detail')->class($labelClasses) !!}
                    <input type="text" id="booking_info_consignee_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from consignee" disabled>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">Cancel</a>
                {!! html()->submit('Save Booking Info')->class($submitClasses) !!}
            </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>
