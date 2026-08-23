
<x-app-layout :title="'Container Activity'">
    @php
        $isNew = ! $record->exists;
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $disabledClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-gray-100 px-3 py-2.5 text-sm text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $navBtnClasses = 'inline-flex items-center justify-center rounded-lg p-1.5 text-topbar-muted transition-all hover:bg-primary-50 hover:text-primary-600 disabled:opacity-40 disabled:cursor-not-allowed';
    @endphp

    <div class="mx-auto max-w-full space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.box', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Container Activity</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Create and manage container activities.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            <div class="mb-6 flex items-center justify-center gap-1">
                <button type="button" id="nav-first" class="nav-btn {{ $navBtnClasses }}" title="First" data-direction="first" {{ $firstId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m11 17-5-5 5-5"/><path d="m18 17-5-5 5-5"/></svg>
                </button>
                <button type="button" id="nav-prev" class="nav-btn {{ $navBtnClasses }}" title="Previous" data-direction="prev" {{ $prevId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m15 18-6-6 6-6"/></svg>
                </button>
                <button type="button" id="nav-new" class="{{ $navBtnClasses }} ml-2" title="New" onclick="newRecord()">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                </button>
                <button type="button" id="nav-next" class="nav-btn {{ $navBtnClasses }}" title="Next" data-direction="next" {{ $nextId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m9 18 6-6-6-6"/></svg>
                </button>
                <button type="button" id="nav-last" class="nav-btn {{ $navBtnClasses }}" title="Last" data-direction="last" {{ $lastId ? '' : 'disabled' }}>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-5 w-5"><path d="m13 17 5-5-5-5"/><path d="m6 17 5-5-5-5"/></svg>
                </button>
                <span class="ml-3 text-sm font-medium text-topbar-muted">
                    <span id="nav-current">{{ $current }}</span> of <span id="nav-total">{{ $total }}</span>
                </span>
            </div>

            {!! html()->form($isNew ? 'POST' : 'PATCH', $isNew ? route('container-activities.store') : route('container-activities.update', $record))->id('header-form')->class('space-y-5')->open() !!}

                {{-- Row 1: Doc#, Activity Date, Current Agent, Activity --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="doc_no" class="{{ $labelClasses }}">Doc #</label>
                        {!! html()->text('doc_no', $record->doc_no)->class($inputClasses) !!}
                        @error('doc_no') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="activity_date" class="{{ $labelClasses }}">Activity Date</label>
                        {!! html()->date('activity_date', $record->activity_date?->format('Y-m-d'))->class($inputClasses) !!}
                        @error('activity_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="agent" class="{{ $labelClasses }}">Current Agent</label>
                        {!! html()->text('agent', $record->agent)->class($inputClasses) !!}
                        @error('agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('Activity', 'activity')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="activity" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select activity</option>
                                @foreach ($activityTypes as $type)
                                    <option value="{{ $type }}" {{ old('activity', $record->activity) == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('activity') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 2: Free Days, BL Number, Booking#, Final Destination --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="free_days" class="{{ $labelClasses }}">Free Days</label>
                        {!! html()->number('free_days', $record->free_days)->class($inputClasses)->attribute('min', 0) !!}
                        @error('free_days') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="bl_number" class="{{ $labelClasses }}">BL Number</label>
                        {!! html()->text('bl_number', $record->bl_number)->class($inputClasses) !!}
                        @error('bl_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="booking_number" class="{{ $labelClasses }}">Booking #</label>
                        {!! html()->text('booking_number', $record->booking_number)->class($inputClasses) !!}
                        @error('booking_number') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('Final Destination', 'final_destination_code')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="final_destination_code" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select port</option>
                                @foreach ($pols as $pol)
                                    <option value="{{ $pol->id }}" {{ old('final_destination_code', $record->final_destination_code) == $pol->id ? 'selected' : '' }}>{{ $pol->city }} ({{ $pol->country }})</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('final_destination_code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 3: POD, Destination Agent, Thru BL, Sailing Date --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('POD', 'pod_code')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="pod_code" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select port</option>
                                @foreach ($pols as $pol)
                                    <option value="{{ $pol->id }}" {{ old('pod_code', $record->pod_code) == $pol->id ? 'selected' : '' }}>{{ $pol->city }} ({{ $pol->country }})</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('pod_code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('Destination Agent', 'destination_agent')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="destination_agent" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select agent</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->code }}" {{ old('destination_agent', $record->destination_agent) == $agent->code ? 'selected' : '' }}>{{ $agent->code }} - {{ $agent->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('destination_agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <div class="flex items-center gap-1">
                            <span class="text-sm font-medium text-topbar-text">Thru BL</span>
                            <span class="text-xs text-topbar-muted">(Carrier's Responsibility)</span>
                        </div>
                        <label class="flex items-center gap-2 cursor-pointer mt-1.5">
                            <input type="hidden" name="thru_bl" value="0">
                            <input type="checkbox" name="thru_bl" value="1" class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500" {{ old('thru_bl', $record->thru_bl) ? 'checked' : '' }}>
                            <span class="text-sm text-topbar-text">Yes</span>
                        </label>
                        @error('thru_bl') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="sailing_date" class="{{ $labelClasses }}">Sailing Date</label>
                        {!! html()->date('sailing_date', $record->sailing_date?->format('Y-m-d'))->class($inputClasses) !!}
                        @error('sailing_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 4: Vessel (FK), Voyage (display), Location, Carrier --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('Vessel', 'vessel_voyage_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="vessel_voyage_id" id="vessel_voyage_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="updateVesselVoyage(this, 'voyage_number_display')">
                                <option value="">Select a vessel</option>
                                @foreach ($vessels as $vessel)
                                    <option value="{{ $vessel->id }}" data-voyage="{{ $vessel->voyage_number }}" {{ old('vessel_voyage_id', $record->vessel_voyage_id) == $vessel->id ? 'selected' : '' }}>{{ $vessel->vessel_name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('vessel_voyage_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="{{ $labelClasses }}">Voyage</label>
                        <input type="text" id="voyage_number_display" class="{{ $disabledClasses }}" disabled value="{{ old('vessel_voyage_id') ? $vessels->find(old('vessel_voyage_id'))?->voyage_number : $record->vesselVoyage?->voyage_number }}">
                        <input type="hidden" name="voyage_number" id="voyage_number_hidden" value="{{ $record->voyage_number }}">
                    </div>

                    <div>
                        <label for="location" class="{{ $labelClasses }}">Location</label>
                        {!! html()->text('location', $record->location)->class($inputClasses) !!}
                        @error('location') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('Carrier', 'carrier')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="carrier" class="{{ $selectClasses }} appearance-none cursor-pointer">
                                <option value="">Select carrier</option>
                                @foreach ($carriers as $carrier)
                                    <option value="{{ $carrier->code }}" {{ old('carrier', $record->carrier) == $carrier->code ? 'selected' : '' }}>{{ $carrier->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('carrier') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 5: TS(1) Port dropdown + description, TS(1) Agent dropdown + description --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('TS (1) Port', 'ts_1_port_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="ts_1_port_id" id="ts_1_port_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="syncTsDescription(this, 'ts_1_port', 'ts_1_port_desc')">
                                <option value="">Select port</option>
                                @foreach ($pols as $pol)
                                    <option value="{{ $pol->id }}" data-desc="{{ $pol->country }}, {{ $pol->city }}" {{ old('ts_1_port_id', $record->ts_1_port_id) == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('ts_1_port_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ts_1_port_desc" class="block text-sm font-medium invisible">&nbsp;</label>
                        <input type="text" id="ts_1_port_desc" class="{{ $disabledClasses }}" disabled value="{{ $record->ts_1_port }}">
                        <input type="hidden" name="ts_1_port" id="ts_1_port" value="{{ $record->ts_1_port }}">
                        @error('ts_1_port') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('TS (1) Agent', 'ts_1_agent_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="ts_1_agent_id" id="ts_1_agent_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="syncTsDescription(this, 'ts_1_agent', 'ts_1_agent_desc')">
                                <option value="">Select agent</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" data-desc="{{ $agent->name }}" {{ old('ts_1_agent_id', $record->ts_1_agent_id) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('ts_1_agent_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ts_1_agent_desc" class="block text-sm font-medium invisible">&nbsp;</label>
                        <input type="text" id="ts_1_agent_desc" class="{{ $disabledClasses }}" disabled value="{{ $record->ts_1_agent }}">
                        <input type="hidden" name="ts_1_agent" id="ts_1_agent" value="{{ $record->ts_1_agent }}">
                        @error('ts_1_agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 6: TS(2) Port dropdown + description, TS(2) Agent dropdown + description --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('TS (2) Port', 'ts_2_port_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="ts_2_port_id" id="ts_2_port_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="syncTsDescription(this, 'ts_2_port', 'ts_2_port_desc')">
                                <option value="">Select port</option>
                                @foreach ($pols as $pol)
                                    <option value="{{ $pol->id }}" data-desc="{{ $pol->country }}, {{ $pol->city }}" {{ old('ts_2_port_id', $record->ts_2_port_id) == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('ts_2_port_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ts_2_port_desc" class="block text-sm font-medium invisible">&nbsp;</label>
                        <input type="text" id="ts_2_port_desc" class="{{ $disabledClasses }}" disabled value="{{ $record->ts_2_port }}">
                        <input type="hidden" name="ts_2_port" id="ts_2_port" value="{{ $record->ts_2_port }}">
                        @error('ts_2_port') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('TS (2) Agent', 'ts_2_agent_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="ts_2_agent_id" id="ts_2_agent_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="syncTsDescription(this, 'ts_2_agent', 'ts_2_agent_desc')">
                                <option value="">Select agent</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" data-desc="{{ $agent->name }}" {{ old('ts_2_agent_id', $record->ts_2_agent_id) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('ts_2_agent_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ts_2_agent_desc" class="block text-sm font-medium invisible">&nbsp;</label>
                        <input type="text" id="ts_2_agent_desc" class="{{ $disabledClasses }}" disabled value="{{ $record->ts_2_agent }}">
                        <input type="hidden" name="ts_2_agent" id="ts_2_agent" value="{{ $record->ts_2_agent }}">
                        @error('ts_2_agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 7: TS(3) Port dropdown + description, TS(3) Agent dropdown + description --}}
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('TS (3) Port', 'ts_3_port_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="ts_3_port_id" id="ts_3_port_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="syncTsDescription(this, 'ts_3_port', 'ts_3_port_desc')">
                                <option value="">Select port</option>
                                @foreach ($pols as $pol)
                                    <option value="{{ $pol->id }}" data-desc="{{ $pol->country }}, {{ $pol->city }}" {{ old('ts_3_port_id', $record->ts_3_port_id) == $pol->id ? 'selected' : '' }}>{{ $pol->port_code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('ts_3_port_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ts_3_port_desc" class="block text-sm font-medium invisible">&nbsp;</label>
                        <input type="text" id="ts_3_port_desc" class="{{ $disabledClasses }}" disabled value="{{ $record->ts_3_port }}">
                        <input type="hidden" name="ts_3_port" id="ts_3_port" value="{{ $record->ts_3_port }}">
                        @error('ts_3_port') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('TS (3) Agent', 'ts_3_agent_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="ts_3_agent_id" id="ts_3_agent_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="syncTsDescription(this, 'ts_3_agent', 'ts_3_agent_desc')">
                                <option value="">Select agent</option>
                                @foreach ($agents as $agent)
                                    <option value="{{ $agent->id }}" data-desc="{{ $agent->name }}" {{ old('ts_3_agent_id', $record->ts_3_agent_id) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6" /></svg>
                            </div>
                        </div>
                        @error('ts_3_agent_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="ts_3_agent_desc" class="block text-sm font-medium invisible">&nbsp;</label>
                        <input type="text" id="ts_3_agent_desc" class="{{ $disabledClasses }}" disabled value="{{ $record->ts_3_agent }}">
                        <input type="hidden" name="ts_3_agent" id="ts_3_agent" value="{{ $record->ts_3_agent }}">
                        @error('ts_3_agent') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Row 8: Remarks (full width) grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 --}}
                <div class="">
                    <div class="sm:col-span-2 lg:col-span-4">
                        <label for="remarks" class="{{ $labelClasses }}">Remarks</label>
                        {!! html()->textarea('remarks', $record->remarks)->class($inputClasses . ' h-24 resize-none') !!}
                        @error('remarks') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between gap-3 pt-2">
                    <a href="{{ route('container-activities.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    {!! html()->submit($isNew ? 'Create Activity' : 'Save changes')->class($submitClasses . ' w-auto px-6') !!}
                </div>

            {!! html()->form()->close() !!}
        </div>

        <div id="details-section" class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8 @if($isNew) hidden @endif">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-800">Container Details</h2>
                <button type="button" onclick="window.CAD.addRow()" class="inline-flex items-center gap-1.5 rounded-lg bg-primary-900 px-3 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                    Add Row
                </button>
            </div>

            <div id="details-grid" class="ag-theme-quartz" style="width: 100%; height: 400px;"></div>
        </div>
    </div>

    <script type="text/javascript">
        const vesselsData = @json($vessels->pluck('voyage_number', 'id'));

        const activityId = {{ $record->id ?? 'null' }};
        const isNew = {{ $isNew ? 'true' : 'false' }};
        // Mutable flags mirror the header form state (New vs editing an existing
        // record). `isNew` above is baked in at render time, so the runtime path
        // uses these instead.
        let createMode = isNew;
        let currentActivityId = activityId;
        const navData = {
            total:   {{ $total }},
            current: {{ $current }},
            firstId: {{ $firstId ?? 'null' }},
            lastId:  {{ $lastId ?? 'null' }},
            prevId:  {{ $prevId ?? 'null' }},
            nextId:  {{ $nextId ?? 'null' }},
        };

        const gridConfig = {
            activityId:          activityId,
            vesselNames:         @json($vesselNames ?? []),
            vesselVoyageLookup:  @json($vesselVoyageLookup ?? []),
            statuses:            @json($statuses ?? []),
            cargoTypes:          @json($cargoTypes ?? []),
            details:             @json($record->details->map(fn ($d) => $d->toArray())),
        };

        document.addEventListener('DOMContentLoaded', function () {
            updateNavButtons();
            syncHiddenVoyage();
            // Initial sync of TS port/agent descriptions from any pre-selected dropdown values.
            document.querySelectorAll('[id$="_port_id"], [id$="_agent_id"]').forEach(sel => {
                const m = sel.id.match(/^(ts_\d+)_(port|agent)_id$/);
                if (m) syncTsDescription(sel, `${m[1]}_${m[2]}`, `${m[1]}_${m[2]}_desc`);
            });
            // Initialise the container-details agGrid (only when a record exists;
            // the section is hidden on the create screen). Guard against a missing
            // global so a script load failure degrades to a logged warning instead
            // of silently breaking the screen.
            const gridEl = document.getElementById('details-grid');
            if (gridEl && window.CAD && !isNew) {
                try {
                    window.CAD.init(gridEl, gridConfig);
                } catch (e) {
                    console.error('Container details grid failed to initialise:', e);
                }
            }

            // Persist any in-editor grid rows when the header form is submitted so
            // rows are not silently lost. Exposes the grid's batch-save on CAD.
            const form = document.getElementById('header-form');
            if (form && window.CAD && typeof window.CAD.saveAll === 'function') {
                form.addEventListener('submit', async function (e) {
                    // On the create screen there is no persisted activity yet, so grid
                    // rows cannot be saved until after the header is stored. Skip the
                    // batch save here; the post-store handler attaches them afterwards.
                    if (!createMode && currentActivityId && typeof window.CAD.saveAll === 'function') {
                        const ok = await window.CAD.saveAll();
                        if (ok === false) e.preventDefault();
                    }
                });
            }
        });

        function updateVesselVoyage(select, targetId) {
            const selected = select.options[select.selectedIndex];
            const voyage   = selected.getAttribute('data-voyage') || '';
            const target   = document.getElementById(targetId);
            const hidden   = document.getElementById('voyage_number_hidden');
            if (target) target.value = voyage;
            if (hidden) hidden.value = voyage;
        }

        function syncHiddenVoyage() {
            const sel = document.getElementById('vessel_voyage_id');
            const hidden = document.getElementById('voyage_number_hidden');
            if (!sel || !hidden) return;
            if (sel.value && !hidden.value) {
                hidden.value = vesselsData[sel.value] || '';
            }
        }

        /**
         * Mirror the chosen dropdown option's data-desc into both the
         * visible (disabled) description input and the hidden text field
         * that gets posted with the form. Clears both when the dropdown
         * is reset to its empty option.
         */
        function syncTsDescription(select, hiddenName, descId) {
            const selected = select.options[select.selectedIndex];
            const desc     = selected ? (selected.getAttribute('data-desc') || '') : '';
            const descEl   = document.getElementById(descId);
            const hiddenEl = document.getElementById(hiddenName);
            if (descEl)   descEl.value   = desc;
            if (hiddenEl) hiddenEl.value = desc;
        }

        function newRecord() {
            const form = document.getElementById('header-form');
            form.querySelectorAll('input:not([type="hidden"]):not([type="checkbox"]), select, textarea').forEach(el => {
                if (el.name === '_token') return;
                el.value = '';
            });
            // The TS port/agent hidden text fields are mirrors of the dropdowns
            // and must be cleared alongside them on a fresh record.
            ['ts_1_port', 'ts_1_agent', 'ts_2_port', 'ts_2_agent', 'ts_3_port', 'ts_3_agent'].forEach(name => {
                const el = document.getElementById(name);
                if (el) el.value = '';
            });
            form.querySelectorAll('input[type="checkbox"]').forEach(el => el.checked = false);
            form.action = '{{ route('container-activities.store') }}';
            form.method = 'POST';
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();

            // Runtime state now reflects a fresh (create) record.
            createMode = true;
            currentActivityId = null;
            if (window.CAD && typeof window.CAD.setActivityId === 'function') {
                window.CAD.setActivityId(null);
            }

            document.getElementById('nav-current').textContent = navData.total + 1;
            document.getElementById('nav-total').textContent  = navData.total;

            window.history.replaceState({}, '', '{{ route('container-activities.create') }}');

            navData.current = navData.total + 1;
            navData.firstId = null;
            navData.lastId  = null;
            navData.prevId  = null;
            navData.nextId  = null;
            updateNavButtons();

            // No detail rows on a fresh record.
            if (window.CAD && typeof window.CAD.setData === 'function') {
                window.CAD.setData([]);
            }
        }

        document.querySelectorAll('.nav-btn').forEach(btn => {
            btn.addEventListener('click', async function (e) {
                e.preventDefault();
                const direction = this.dataset.direction;
                const targetId  = navData[direction + 'Id'];
                if (!targetId) return;

                try {
                    const resp = await fetch(`/container-activities/${targetId}/navigate`, {
                        headers: { 'Accept': 'application/json' },
                    });
                    const data = await resp.json();
                    if (resp.ok) {
                        updateFormWithData(data);
                    }
                } catch (err) {
                    console.error('Navigation error:', err);
                }
            });
        });

        function updateFormWithData(data) {
            const act = data.activity;

            document.querySelector('input[name="doc_no"]').value                 = act.doc_no || '';
            document.querySelector('input[name="activity_date"]').value          = act.activity_date || '';
            document.querySelector('input[name="agent"]').value                  = act.agent || '';
            document.querySelector('select[name="activity"]').value              = act.activity || '';
            document.querySelector('input[name="free_days"]').value              = act.free_days || '';
            document.querySelector('input[name="bl_number"]').value              = act.bl_number || '';
            document.querySelector('input[name="booking_number"]').value         = act.booking_number || '';
            document.querySelector('select[name="final_destination_code"]').value = act.final_destination_code || '';
            document.querySelector('select[name="pod_code"]').value              = act.pod_code || '';
            document.querySelector('select[name="destination_agent"]').value     = act.destination_agent || '';
            document.querySelector('input[name="thru_bl"]').checked              = !!act.thru_bl;
            document.querySelector('input[name="sailing_date"]').value           = act.sailing_date || '';
            document.querySelector('select[name="vessel_voyage_id"]').value      = act.vessel_voyage_id || '';
            document.querySelector('input[name="voyage_number"]').value          = act.voyage_number || '';
            document.querySelector('input[name="location"]').value               = act.location || '';
            document.querySelector('select[name="carrier"]').value               = act.carrier || '';
            document.querySelector('input[name="ts_1_port"]').value              = act.ts_1_port || '';
            document.querySelector('input[name="ts_1_agent"]').value             = act.ts_1_agent || '';
            document.querySelector('input[name="ts_2_port"]').value              = act.ts_2_port || '';
            document.querySelector('input[name="ts_2_agent"]').value             = act.ts_2_agent || '';
            document.querySelector('input[name="ts_3_port"]').value              = act.ts_3_port || '';
            document.querySelector('input[name="ts_3_agent"]').value             = act.ts_3_agent || '';

            // TS port/agent FK dropdowns + their disabled description mirrors.
            [
                ['ts_1_port_id', 'ts_1_port',     'ts_1_port_desc'],
                ['ts_2_port_id', 'ts_2_port',     'ts_2_port_desc'],
                ['ts_3_port_id', 'ts_3_port',     'ts_3_port_desc'],
                ['ts_1_agent_id','ts_1_agent',    'ts_1_agent_desc'],
                ['ts_2_agent_id','ts_2_agent',    'ts_2_agent_desc'],
                ['ts_3_agent_id','ts_3_agent',    'ts_3_agent_desc'],
            ].forEach(([selectName, hiddenName, descId]) => {
                const sel   = document.querySelector(`select[name="${selectName}"]`);
                const val   = act[selectName] != null ? String(act[selectName]) : '';
                if (sel) sel.value = val;
                const desc = act[hiddenName] || '';
                const descEl   = document.getElementById(descId);
                const hiddenEl = document.getElementById(hiddenName);
                if (descEl)   descEl.value   = desc;
                if (hiddenEl) hiddenEl.value = desc;
            });
            document.querySelector('textarea[name="remarks"]').value             = act.remarks || '';

            const voyageDisplay = document.getElementById('voyage_number_display');
            if (voyageDisplay) {
                voyageDisplay.value = (act.vessel_voyage && act.vessel_voyage.voyage_number) || act.voyage_number || '';
            }

            const form = document.getElementById('header-form');
            form.action = `/container-activities/${act.id}`;
            form.method = 'POST';
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type  = 'hidden';
                methodInput.name  = '_method';
                methodInput.value = 'PATCH';
                form.appendChild(methodInput);
            } else {
                methodInput.value = 'PATCH';
            }

            // Runtime state now reflects an existing (edit) record.
            createMode = false;
            currentActivityId = act.id;
            if (window.CAD && typeof window.CAD.setActivityId === 'function') {
                window.CAD.setActivityId(act.id);
            }

            document.getElementById('nav-current').textContent = data.current;
            document.getElementById('nav-total').textContent   = data.total;

            navData.total   = data.total;
            navData.current = data.current;
            navData.firstId = data.firstId;
            navData.lastId  = data.lastId;
            navData.prevId  = data.prevId;
            navData.nextId  = data.nextId;
            updateNavButtons();

            // Refresh the container-details grid with this activity's detail rows.
            if (window.CAD && typeof window.CAD.setData === 'function') {
                window.CAD.setData(data.details || []);
            }

            window.history.replaceState({}, '', `/container-activities/${act.id}/edit`);
        }

        function updateNavButtons() {
            const updateBtn = (id, selector) => {
                const el = document.getElementById(selector);
                if (!el) return;
                if (id) {
                    el.removeAttribute('disabled');
                    el.style.pointerEvents = '';
                    el.style.opacity = '';
                } else {
                    el.setAttribute('disabled', '');
                    el.style.pointerEvents = 'none';
                    el.style.opacity = '0.4';
                }
            };
            updateBtn(navData.firstId, 'nav-first');
            updateBtn(navData.prevId,  'nav-prev');
            updateBtn(navData.nextId,  'nav-next');
            updateBtn(navData.lastId,  'nav-last');
        }
    </script>
</x-app-layout>
