<x-app-layout :title="'Edit Booking'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition appearance-none cursor-pointer';
        $disabledClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-gray-100 px-3 py-2.5 text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
    @endphp

    <div class="mx-auto max-w-full space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    @include('components.icons.calendar', ['classes' => 'h-7 w-7 text-primary-600'])
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Edit Booking</h1>
                        <p class="mt-1 text-sm text-topbar-muted">Update the booking details below.</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">Cancel</a>
        </div>

        <div class="rounded-2xl bg-card-bg shadow-lg ring-1 ring-card-border">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4 sm:px-8">
                <h2 class="text-lg font-semibold text-gray-800">Booking Details</h2>
            </div>

            <div class="p-6 sm:p-8">
                {{-- Tabs Navigation --}}
                <div class="mb-8 border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
                        <li class="mr-2">
                            <a href="#basic-info" class="inline-block p-4 border-b-2 border-primary-600 text-primary-600 rounded-t-lg tab-link active">Basic Info</a>
                        </li>
                        <li class="mr-2">
                            <a href="#other-info" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg tab-link">Other Info</a>
                        </li>
                        <li class="mr-2">
                            <a href="#message" class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg tab-link">Message</a>
                        </li>
                    </ul>
                </div>

                    {{-- Basic Info Tab --}}
                    <div id="basic-info" class="tab-pane space-y-5">
                        {!! html()->form('PATCH', route('bookings.update', $booking))->id('booking-form')->open() !!}
                        @csrf
                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label for="booking_no" class="{{ $labelClasses }}">Booking # <span class="text-red-500">*</span></label>
                                {!! html()->text('booking_no', old('booking_no', $booking->booking_no))->class($inputClasses)->required() !!}
                                @error('booking_no')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="approval_no" class="{{ $labelClasses }}">Approval # <span class="text-red-500">*</span></label>
                                {!! html()->text('approval_no', old('approval_no', $booking->approval_no))->class($inputClasses)->required() !!}
                                @error('approval_no')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="reference_no" class="{{ $labelClasses }}">Reference # <span class="text-red-500">*</span></label>
                                {!! html()->text('reference_no', old('reference_no', $booking->reference_no))->class($inputClasses)->required() !!}
                                @error('reference_no')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="booking_date" class="{{ $labelClasses }}">Booking Date <span class="text-red-500">*</span></label>
                                {!! html()->date('booking_date', old('booking_date', $booking->booking_date?->format('Y-m-d')))->class($inputClasses)->required() !!}
                                @error('booking_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Carrier', 'carrier')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="carrier" id="carrier" class="{{ $selectClasses }}">
                                        <option value="">Select Carrier</option>
                                        @foreach ($carriers as $carrier)
                                            <option value="{{ $carrier->id }}" data-detail="{{ $carrier->name }}" {{ old('carrier', $booking->getRawOriginal('carrier')) == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                {!! html()->label('Cntr Owner', 'cntr_owner')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="cntr_owner" id="cntr_owner" class="{{ $selectClasses }}">
                                        <option value="">Select Owner</option>
                                        @foreach ($cntrOwners as $value => $label)
                                            <option value="{{ $value }}" {{ old('cntr_owner', $booking->cntr_owner) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="sailing_date" class="{{ $labelClasses }}">Sailing Date <span class="text-red-500">*</span></label>
                                {!! html()->date('sailing_date', old('sailing_date', $booking->sailing_date?->format('Y-m-d')))->class($inputClasses)->required() !!}
                                @error('sailing_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div class="flex items-center justify-start">
                                <label class="flex items-center gap-2 text-sm font-medium text-topbar-text">
                                    {!! html()->checkbox('thru_bl', old('thru_bl', $booking->thru_bl)) !!}
                                    Thru BL
                                </label>
                            </div>
                        </div>

                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Commodity', 'commodity')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="commodity" id="commodity" class="{{ $selectClasses }}">
                                        <option value="">Select Commodity</option>
                                        @foreach ($commodities as $commodity)
                                            <option value="{{ $commodity->id }}" data-detail="{{ $commodity->commodity_number }}" {{ old('commodity', $booking->getRawOriginal('commodity')) == $commodity->id ? 'selected' : '' }}>{{ $commodity->commodity_number }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                {!! html()->label('DG Status', 'non_dg')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="non_dg" id="non_dg" class="{{ $selectClasses }}">
                                        <option value="">Select Status</option>
                                        @foreach (config('dropdowns.bookings.non_dg') as $value => $label)
                                            <option value="{{ $value }}" {{ old('non_dg', $booking->non_dg) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                {!! html()->label('Vessel / Voyage', 'vessel_voyage')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="vessel_voyage" id="vessel_voyage" class="{{ $selectClasses }}">
                                        <option value="">Select Vessel / Voyage</option>
                                        @foreach ($vesselVoyages as $vv)
                                            <option value="{{ $vv->id }}" data-detail="{{ $vv->voyage_number }}" {{ old('vessel_voyage', $booking->vessel_voyage) == $vv->id ? 'selected' : '' }}>{{ $vv->vessel_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="vessel_voyage_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="vessel_voyage_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('POL', 'pol')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="pol" id="pol" class="{{ $selectClasses }}">
                                        <option value="">Select POL</option>
                                        @foreach ($pols as $pol)
                                            <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('pol', $booking->getRawOriginal('pol')) == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="pol_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="pol_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Agent POL', 'agent_pol')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="agent_pol" id="agent_pol" class="{{ $selectClasses }}">
                                        <option value="">Select Agent POL</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('agent_pol', $booking->agent_pol) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="agent_pol_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="agent_pol_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('POFD', 'pofd')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="pofd" id="pofd" class="{{ $selectClasses }}">
                                        <option value="">Select POFD</option>
                                        @foreach ($pofds as $pod)
                                            <option value="{{ $pod->id }}" data-detail="{{ $pod->city }}, {{ $pod->country }}" {{ old('pofd', $booking->getRawOriginal('pofd')) == $pod->id ? 'selected' : '' }}>{{ $pod->location_code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="pofd_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="pofd_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Agent POFD', 'agent_pofd')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="agent_pofd" id="agent_pofd" class="{{ $selectClasses }}">
                                        <option value="">Select Agent POFD</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('agent_pofd', $booking->agent_pofd) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="agent_pofd_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="agent_pofd_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('POT (1)', 'pot_1')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="pot_1" id="pot_1" class="{{ $selectClasses }}">
                                        <option value="">Select POT (1)</option>
                                        @foreach ($pols as $pol)
                                            <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('pot_1', $booking->pot_1) == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="pot_1_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="pot_1_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Agent 1', 'agent_1')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="agent_1" id="agent_1" class="{{ $selectClasses }}">
                                        <option value="">Select Agent 1</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('agent_1', $booking->agent_1) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="agent_1_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="agent_1_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('POT (2)', 'pot_2')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="pot_2" id="pot_2" class="{{ $selectClasses }}">
                                        <option value="">Select POT (2)</option>
                                        @foreach ($pols as $pol)
                                            <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('pot_2', $booking->pot_2) == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="pot_2_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="pot_2_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Agent 2', 'agent_2')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="agent_2" id="agent_2" class="{{ $selectClasses }}">
                                        <option value="">Select Agent 2</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('agent_2', $booking->agent_2) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="agent_2_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="agent_2_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Shipper/BP', 'shipper_bp')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="shipper_bp" id="shipper_bp" class="{{ $selectClasses }}">
                                        <option value="">Select Shipper/BP</option>
                                        @foreach ($shipperBps as $shipperBp)
                                            <option value="{{ $shipperBp->id }}" data-detail="{{ $shipperBp->name }}" {{ old('shipper_bp', $booking->shipper_bp) == $shipperBp->id ? 'selected' : '' }}>{{ $shipperBp->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="shipper_bp_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="shipper_bp_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Freight Type', 'freight_type')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="freight_type" id="freight_type" class="{{ $selectClasses }}">
                                        <option value="">Select Freight Type</option>
                                        @foreach ($freightTypes as $value => $label)
                                            <option value="{{ $value }}" {{ old('freight_type', $booking->freight_type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                {!! html()->label('&nbsp;', 'freight_type_sub')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="freight_type_sub" id="freight_type_sub" class="{{ $selectClasses }}">
                                        <option value="">Select Freight Type Sub</option>
                                        @foreach ($freightTypeSubs as $value => $label)
                                            <option value="{{ $value }}" {{ old('freight_type_sub', $booking->freight_type_sub) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Consignee', 'consignee')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="consignee" id="consignee" class="{{ $selectClasses }}">
                                        <option value="">Select Consignee</option>
                                        @foreach ($parties as $party)
                                            <option value="{{ $party->id }}" data-detail="{{ $party->name }}" {{ old('consignee', $booking->consignee) == $party->id ? 'selected' : '' }}>{{ $party->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="consignee_detail" class="{{ $labelClasses }}">&nbsp;</label>
                                <input type="text" id="consignee_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {{-- Empty --}}
                            </div>
                            <div>
                                {{-- Empty --}}
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-8">
                            <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="m12 19-7-7 7-7" />
                                    <path d="M19 12H5" />
                                </svg>
                                Cancel
                            </a>
                            {!! html()->submit('Save Booking')->class($submitClasses . ' w-auto') !!}
                        </div>
                        {!! html()->form()->close() !!}
                        @include('bookings._booking-details')
                    </div>

                    {{-- Other Info Tab --}}
                    <div id="other-info" class="tab-pane hidden space-y-5">
                        <form method="POST" action="{{ route('bookings.other-info.update', $booking) }}">
                            @csrf
                            @method('PATCH')
                        <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="sm:col-span-2 lg:col-span-4">
                                {!! html()->label('Special Request', 'special_req')->class($labelClasses) !!}
                                {!! html()->textarea('special_req', old('special_req', $booking->otherInfo?->special_req))->class($inputClasses . ' min-h-24')->required() !!}
                            </div>
                            <div>
                                {!! html()->label('Free Days POL', 'free_days_pol')->class($labelClasses) !!}
                                {!! html()->number('free_days_pol', old('free_days_pol', $booking->otherInfo?->free_days_pol))->class($inputClasses)->attribute('min', 0) !!}
                            </div>
                            <div>
                                {!! html()->label('Detention Free Days POFD', 'detention_free_pofd')->class($labelClasses) !!}
                                {!! html()->number('detention_free_pofd', old('detention_free_pofd', $booking->otherInfo?->detention_free_pofd))->class($inputClasses)->attribute('min', 0) !!}
                            </div>
                            <div>
                                <label class="{{ $labelClasses }}">&nbsp;</label>
                                <div class="mt-1.5 flex items-center gap-2">
                                    <label for="detention_tariff" class="flex items-center gap-2 cursor-pointer">
                                        {!! html()->checkbox('detention_tariff', old('detention_tariff', $booking->otherInfo?->detention_tariff))->id('detention_tariff')->class('rounded border-gray-300 text-primary-600 focus:ring-primary-500') !!}
                                        <span class="text-sm text-topbar-text">Detention Tariff: Special</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                {!! html()->label('Detention Currency', 'detention_currency')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="detention_currency" id="detention_currency" class="{{ $selectClasses }}">
                                        <option value="">Select Currency</option>
                                        @foreach ($currencies as $code => $label)
                                            <option value="{{ $code }}" {{ old('detention_currency', $booking->otherInfo?->detention_currency) == $code ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-8">
                            <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="m12 19-7-7 7-7" />
                                    <path d="M19 12H5" />
                                </svg>
                                Cancel
                            </a>
                            {!! html()->submit('Save Booking')->class($submitClasses . ' w-auto') !!}
                        </div>
                        </form>
                    </div>

                    {{-- Message Tab --}}
                    <div id="message" class="tab-pane hidden space-y-5">
                        <form method="POST" action="{{ route('bookings.message.update', $booking) }}">
                            @csrf
                            @method('PATCH')
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                {!! html()->label('Message', 'message')->class($labelClasses) !!}
                                {!! html()->textarea('message', old('message', $booking->otherInfo?->message))->class($inputClasses . ' min-h-32') !!}
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 pt-8">
                            <a href="{{ route('bookings.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                    <path d="m12 19-7-7 7-7" />
                                    <path d="M19 12H5" />
                                </svg>
                                Cancel
                            </a>
                            {!! html()->submit('Save Booking')->class($submitClasses . ' w-auto') !!}
                        </div>
                        </form>
                    </div>
            </div>
        </div>
    </div>
</x-app-layout>

