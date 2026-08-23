<x-app-layout :title="'Create Booking'">
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
                        <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Create Booking</h1>
                        <p class="mt-1 text-sm text-topbar-muted">Fill in the booking details below.</p>
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

                {!! html()->form('POST', route('bookings.store'))->open() !!}
                    @csrf

                    {{-- Basic Info Tab --}}
                    <div id="basic-info" class="tab-pane space-y-5">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                <label for="booking_no" class="{{ $labelClasses }}">Booking # <span class="text-red-500">*</span></label>
                                {!! html()->text('booking_no', old('booking_no'))->class($inputClasses)->required() !!}
                                @error('booking_no')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="approval_no" class="{{ $labelClasses }}">Approval # <span class="text-red-500">*</span></label>
                                {!! html()->text('approval_no', old('approval_no'))->class($inputClasses)->required() !!}
                                @error('approval_no')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="reference_no" class="{{ $labelClasses }}">Reference # <span class="text-red-500">*</span></label>
                                {!! html()->text('reference_no', old('reference_no'))->class($inputClasses)->required() !!}
                                @error('reference_no')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label for="booking_date" class="{{ $labelClasses }}">Booking Date <span class="text-red-500">*</span></label>
                                {!! html()->date('booking_date', old('booking_date'))->class($inputClasses)->required() !!}
                                @error('booking_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Carrier', 'carrier')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="carrier" id="carrier" class="{{ $selectClasses }}" onchange="syncDetail(this, 'carrier_detail')">
                                        <option value="">Select Carrier</option>
                                        @foreach ($carriers as $carrier)
                                            <option value="{{ $carrier->id }}" data-detail="{{ $carrier->name }}" {{ old('carrier') == $carrier->id ? 'selected' : '' }}>{{ $carrier->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-end pb-1">
                                <label class="flex items-center gap-2 text-sm font-medium text-topbar-text">
                                    {!! html()->checkbox('thru_bl', old('thru_bl', false)) !!}
                                    Thru BL
                                </label>
                            </div>
                            <div>
                                {!! html()->label('Cntr Owner', 'cntr_owner')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="cntr_owner" id="cntr_owner" class="{{ $selectClasses }}">
                                        <option value="">Select Owner</option>
                                        @foreach ($cntrOwners as $value => $label)
                                            <option value="{{ $value }}" {{ old('cntr_owner') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="sailing_date" class="{{ $labelClasses }}">Sailing Date <span class="text-red-500">*</span></label>
                                {!! html()->date('sailing_date', old('sailing_date'))->class($inputClasses)->required() !!}
                                @error('sailing_date')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Commodity', 'commodity')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="commodity" id="commodity" class="{{ $selectClasses }}" onchange="syncDetail(this, 'commodity_detail')">
                                        <option value="">Select Commodity</option>
                                        @foreach ($commodities as $commodity)
                                            <option value="{{ $commodity->id }}" data-detail="{{ $commodity->commodity_number }}" {{ old('commodity') == $commodity->id ? 'selected' : '' }}>{{ $commodity->commodity_number }}</option>
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
                                        <option value="0" {{ old('non_dg') == '0' ? 'selected' : '' }}>NON DG</option>
                                        <option value="1" {{ old('non_dg') == '1' ? 'selected' : '' }}>DG</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                {!! html()->label('Vessel / Voyage', 'vessel_voyage')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="vessel_voyage" id="vessel_voyage" class="{{ $selectClasses }}" onchange="syncDetail(this, 'vessel_voyage_detail')">
                                        <option value="">Select Vessel / Voyage</option>
                                        @foreach ($vesselVoyages as $vv)
                                            <option value="{{ $vv->id }}" data-detail="{{ $vv->voyage_number }}" {{ old('vessel_voyage') == $vv->id ? 'selected' : '' }}>{{ $vv->vessel_name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="vessel_voyage_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="vessel_voyage_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('POL', 'pol')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="pol" id="pol" class="{{ $selectClasses }}" onchange="syncDetail(this, 'pol_detail')">
                                        <option value="">Select POL</option>
                                        @foreach ($pols as $pol)
                                            <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('pol') == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="pol_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="pol_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Agent POL', 'agent_pol')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="agent_pol" id="agent_pol" class="{{ $selectClasses }}" onchange="syncDetail(this, 'agent_pol_detail')">
                                        <option value="">Select Agent POL</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('agent_pol') == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="agent_pol_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="agent_pol_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('POFD', 'pofd')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="pofd" id="pofd" class="{{ $selectClasses }}" onchange="syncDetail(this, 'pofd_detail')">
                                        <option value="">Select POFD</option>
                                        @foreach ($pofds as $pod)
                                            <option value="{{ $pod->id }}" data-detail="{{ $pod->city }}, {{ $pod->country }}" {{ old('pofd') == $pod->id ? 'selected' : '' }}>{{ $pod->location_code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="pofd_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="pofd_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Agent POFD', 'agent_pofd')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="agent_pofd" id="agent_pofd" class="{{ $selectClasses }}" onchange="syncDetail(this, 'agent_pofd_detail')">
                                        <option value="">Select Agent POFD</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('agent_pofd') == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="agent_pofd_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="agent_pofd_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('POT (1)', 'pot_1')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="pot_1" id="pot_1" class="{{ $selectClasses }}" onchange="syncDetail(this, 'pot_1_detail')">
                                        <option value="">Select POT (1)</option>
                                        @foreach ($pols as $pol)
                                            <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('pot_1') == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="pot_1_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="pot_1_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Agent 1', 'agent_1')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="agent_1" id="agent_1" class="{{ $selectClasses }}" onchange="syncDetail(this, 'agent_1_detail')">
                                        <option value="">Select Agent 1</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('agent_1') == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="agent_1_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="agent_1_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('POT (2)', 'pot_2')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="pot_2" id="pot_2" class="{{ $selectClasses }}" onchange="syncDetail(this, 'pot_2_detail')">
                                        <option value="">Select POT (2)</option>
                                        @foreach ($pols as $pol)
                                            <option value="{{ $pol->id }}" data-detail="{{ $pol->city }}, {{ $pol->country }}" {{ old('pot_2') == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="pot_2_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="pot_2_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Agent 2', 'agent_2')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="agent_2" id="agent_2" class="{{ $selectClasses }}" onchange="syncDetail(this, 'agent_2_detail')">
                                        <option value="">Select Agent 2</option>
                                        @foreach ($agents as $agent)
                                            <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('agent_2') == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="agent_2_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="agent_2_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Shipper/BP', 'shipper_bp')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="shipper_bp" id="shipper_bp" class="{{ $selectClasses }}" onchange="syncDetail(this, 'shipper_bp_detail')">
                                        <option value="">Select Shipper/BP</option>
                                        @foreach ($shipperBps as $shipperBp)
                                            <option value="{{ $shipperBp->id }}" data-detail="{{ $shipperBp->name }}" {{ old('shipper_bp') == $shipperBp->id ? 'selected' : '' }}>{{ $shipperBp->code }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="shipper_bp_detail" class="{{ $labelClasses}}">&nbsp;</label>
                                <input type="text" id="shipper_bp_detail" class="{{ $disabledClasses }}" disabled>
                            </div>
                            <div>
                                {!! html()->label('Freight Type', 'freight_type')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="freight_type" id="freight_type" class="{{ $selectClasses }}">
                                        <option value="">Select Freight Type</option>
                                        @foreach ($freightTypes as $value => $label)
                                            <option value="{{ $value }}" {{ old('freight_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                                    </div>
                                </div>
                            </div>
                            <div>
                                {{-- Empty --}}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div>
                                {!! html()->label('Consignee', 'consignee')->class($labelClasses) !!}
                                <div class="relative">
                                    <select name="consignee" id="consignee" class="{{ $selectClasses }}" onchange="syncDetail(this, 'consignee_detail')">
                                        <option value="">Select Consignee</option>
                                        @foreach ($parties as $party)
                                            <option value="{{ $party->id }}" data-detail="{{ $party->name }}" {{ old('consignee') == $party->id ? 'selected' : '' }}>{{ $party->code }}</option>
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
                    </div>

                    {{-- Other Info Tab --}}
                    <div id="other-info" class="tab-pane hidden">
                        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                            <div class="sm:col-span-2 lg:col-span-4">
                                {!! html()->label('Special Requirements', 'special_req')->class($labelClasses) !!}
                                {!! html()->textarea('special_req', old('special_req'))->class($inputClasses . ' min-h-24') !!}
                            </div>
                            <div>
                                {!! html()->label('Free Days POL', 'free_days_pol')->class($labelClasses) !!}
                                {!! html()->number('free_days_pol', old('free_days_pol'))->class($inputClasses)->attribute('min', 0) !!}
                            </div>
                            <div>
                                {!! html()->label('Detention Free POFD', 'detention_free_pofd')->class($labelClasses) !!}
                                {!! html()->number('detention_free_pofd', old('detention_free_pofd'))->class($inputClasses)->attribute('min', 0) !!}
                            </div>
                        </div>
                    </div>

                    {{-- Message Tab --}}
                    <div id="message" class="tab-pane hidden">
                        <div class="grid grid-cols-1 gap-5">
                            <div>
                                {!! html()->label('Message', 'message')->class($labelClasses) !!}
                                {!! html()->textarea('message', old('message'))->class($inputClasses . ' min-h-32') !!}
                            </div>
                        </div>
                    </div>

                    <div class="col-span-full flex items-center justify-between gap-3 pt-8">
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
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function syncDetail(select, targetId) {
            const target = document.getElementById(targetId);
            if (!target) return;
            const selected = select.options[select.selectedIndex];
            target.value = selected ? (selected.getAttribute('data-detail') || '') : '';
        }

        document.addEventListener('DOMContentLoaded', function () {
            const tabs = document.querySelectorAll('.tab-pane');
            const links = document.querySelectorAll('.tab-link');

            links.forEach(link => {
                link.addEventListener('click', function (e) {
                    e.preventDefault();
                    const target = this.getAttribute('href').substring(1);

                    tabs.forEach(tab => tab.classList.add('hidden'));
                    document.getElementById(target).classList.remove('hidden');

                    links.forEach(l => {
                        l.classList.remove('border-primary-600', 'text-primary-600');
                        l.classList.add('border-transparent', 'text-gray-500');
                    });
                    this.classList.remove('border-transparent', 'text-gray-500');
                    this.classList.add('border-primary-600', 'text-primary-600');
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
