<x-app-layout :title="'Create Container Release Order'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition appearance-none cursor-pointer';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
    @endphp

    <div class="mx-auto max-w-full space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-3">
                    @include('components.icons.box', ['classes' => 'h-7 w-7 text-primary-600'])
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Create Container Release Order</h1>
                        <p class="mt-1 text-sm text-topbar-muted">Fill in the container release order details below.</p>
                    </div>
                </div>
            </div>
            <a href="{{ route('container-release-orders.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">Cancel</a>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form('POST', route('container-release-orders.store'))->id('cro-form')->class('space-y-5')->open() !!}

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="booking_no" class="{{ $labelClasses }}">Booking # <span class="text-red-500">*</span></label>
                        {!! html()->text('booking_no', old('booking_no', $prefill['booking_no'] ?? ''))->class($inputClasses)->required() !!}
                        <input type="hidden" name="booking_id" value="{{ old('booking_id', $prefill['booking_id'] ?? '') }}">
                        @error('booking_no') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="reference_no" class="{{ $labelClasses }}">Reference # <span class="text-red-500">*</span></label>
                        {!! html()->text('reference_no', old('reference_no', $prefill['reference_no'] ?? ''))->class($inputClasses)->required() !!}
                        @error('reference_no') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label for="booking_date" class="{{ $labelClasses }}">Booking Date <span class="text-red-500">*</span></label>
                        {!! html()->date('booking_date', old('booking_date', $prefill['booking_date'] ?? ''))->class($inputClasses)->required() !!}
                        @error('booking_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('Cntr Owner', 'cntr_owner')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="cntr_owner" id="cntr_owner" class="{{ $selectClasses }}" required>
                                <option value="">Select Owner</option>
                                @foreach ($cntrOwners as $value => $label)
                                    <option value="{{ $value }}" {{ old('cntr_owner', $prefill['cntr_owner'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                        @error('cntr_owner') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('Commodity', 'commodity_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="commodity_id" id="commodity_id" class="{{ $selectClasses }}" required>
                                <option value="">Select Commodity</option>
                                @foreach ($commodities as $commodity)
                                    <option value="{{ $commodity->id }}" {{ old('commodity_id', $prefill['commodity_id'] ?? '') == $commodity->id ? 'selected' : '' }}>{{ $commodity->commodity_number }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                        @error('commodity_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('DG Status', 'dg_status')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="dg_status" id="dg_status" class="{{ $selectClasses }}" required>
                                <option value="">Select Status</option>
                                @foreach ($dgStatuses as $value => $label)
                                    <option value="{{ $value }}" {{ old('dg_status', $prefill['dg_status'] ?? '') == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                        @error('dg_status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('POL', 'pol_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="pol_id" id="pol_id" class="{{ $selectClasses }}" required>
                                <option value="">Select POL</option>
                                @foreach ($pols as $pol)
                                    <option value="{{ $pol->id }}" {{ old('pol_id', $prefill['pol_id'] ?? '') == $pol->id ? 'selected' : '' }}>{{ $pol->city }} ({{ $pol->country }})</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                        @error('pol_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        {!! html()->label('POFD', 'pofd_id')->class($labelClasses) !!}
                        <div class="relative">
                            <select name="pofd_id" id="pofd_id" class="{{ $selectClasses }}" required>
                                <option value="">Select POFD</option>
                                @foreach ($pofds as $pod)
                                    <option value="{{ $pod->id }}" {{ old('pofd_id', $prefill['pofd_id'] ?? '') == $pod->id ? 'selected' : '' }}>{{ $pod->city }} ({{ $pod->country }})</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                            </div>
                        </div>
                        @error('pofd_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="sm:col-span-2 lg:col-span-4">
                        {!! html()->label('Notes', 'notes')->class($labelClasses) !!}
                        {!! html()->textarea('notes', old('notes'))->class($inputClasses . ' min-h-24') !!}
                        @error('notes') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center justify-between gap-3 pt-2">
                    <a href="{{ route('container-release-orders.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    {!! html()->submit('Create Container Release Order')->class($submitClasses . ' w-auto px-6') !!}
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>