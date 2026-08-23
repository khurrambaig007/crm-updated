<x-app-layout :title="'New Maintenance &amp; Repair Entry'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $disabledClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-gray-100 px-3 py-2.5 text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
    @endphp

    <div class="mx-auto max-w-full space-y-8">
        <div>
            <div class="flex items-center gap-3">
                @include('components.icons.wrench', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">New Maintenance &amp; Repair Entry</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Create a new maintenance and repair record.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form('POST', route('maintenance-repair-entries.store'))->class('grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4')->open() !!}

                <div>
                    <label for="trans_id" class="{{ $labelClasses }}">Transaction # <span class="text-red-500">*</span></label>
                    {!! html()->text('trans_id')->class($inputClasses)->required() !!}
                    @error('trans_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Agent Code 1', 'agent_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="agent_id" id="agent_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="updateAgentName(this, 'agent_name_1')">
                            <option value="">Select an agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" data-name="{{ $agent->name }}" {{ old('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('agent_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Agent Name</label>
                    <input type="text" id="agent_name_1" class="{{ $disabledClasses }}" disabled value="{{ old('agent_id') ? $agents->find(old('agent_id'))?->name : '' }}">
                </div>

                <div>
                    <label for="container_no" class="{{ $labelClasses }}">Container # <span class="text-red-500">*</span></label>
                    {!! html()->text('container_no')->class($inputClasses)->required() !!}
                    @error('container_no')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="ca_doc_no" class="{{ $labelClasses }}">CA Doc # <span class="text-red-500">*</span></label>
                    {!! html()->text('ca_doc_no')->class($inputClasses)->required() !!}
                    @error('ca_doc_no')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Agent Code 2', 'agent_id_2')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="agent_id_2" id="agent_id_2" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="updateAgentName(this, 'agent_name_2')">
                            <option value="">Select an agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" data-name="{{ $agent->name }}" {{ old('agent_id_2') == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('agent_id_2')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Agent Name</label>
                    <input type="text" id="agent_name_2" class="{{ $disabledClasses }}" disabled value="{{ old('agent_id_2') ? $agents->find(old('agent_id_2'))?->name : '' }}">
                </div>

                <div>
                    <label for="owner" class="{{ $labelClasses }}">Owner</label>
                    {!! html()->text('owner')->class($inputClasses) !!}
                    @error('owner')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Vessel Voyage', 'vessel_voyage_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="vessel_voyage_id" id="vessel_voyage_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="updateVesselName(this, 'vessel_name_display')">
                            <option value="">Select a vessel</option>
                            @foreach ($vesselVoyages as $vv)
                                <option value="{{ $vv->id }}" data-voyage="{{ $vv->voyage_number }}" {{ old('vessel_voyage_id') == $vv->id ? 'selected' : '' }}>{{ $vv->vessel_name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('vessel_voyage_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Voyage Number</label>
                    <input type="text" id="vessel_name_display" class="{{ $disabledClasses }}" disabled value="{{ old('vessel_voyage_id') ? $vesselVoyages->find(old('vessel_voyage_id'))?->voyage_number : '' }}">
                </div>

                <div>
                    <label for="load_port" class="{{ $labelClasses }}">Load Port</label>
                    {!! html()->text('load_port')->class($inputClasses) !!}
                    @error('load_port')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="location" class="{{ $labelClasses }}">Location</label>
                    {!! html()->text('location')->class($inputClasses) !!}
                    @error('location')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Liable Party', 'liable_party_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="liable_party_id" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select a party</option>
                            @foreach ($liableParties as $party)
                                <option value="{{ $party->id }}" {{ old('liable_party_id') == $party->id ? 'selected' : '' }}>{{ $party->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('liable_party_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Vendor', 'vendor_id')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="vendor_id" id="vendor_id" class="{{ $selectClasses }} appearance-none cursor-pointer" onchange="updateVendorName(this, 'vendor_name_display')">
                            <option value="">Select a vendor</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" data-name="{{ $vendor->name }}" {{ old('vendor_id') == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('vendor_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="{{ $labelClasses }}">Vendor Name</label>
                    <input type="text" id="vendor_name_display" class="{{ $disabledClasses }}" disabled value="{{ old('vendor_id') ? $vendors->find(old('vendor_id'))?->name : '' }}">
                </div>

                <div>
                    <label for="status" class="{{ $labelClasses }}">Status</label>
                    {!! html()->text('status')->class($inputClasses) !!}
                    @error('status')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-full flex items-center justify-between gap-3 pt-6">
                    <a href="{{ route('maintenance-repair-entries.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    {!! html()->submit('Create entry')->class($submitClasses . ' w-auto') !!}
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>

    <script>
        function updateAgentName(select, targetId) {
            const selected = select.options[select.selectedIndex];
            const name = selected.getAttribute('data-name') || '';
            document.getElementById(targetId).value = name;
        }

        function updateVesselName(select, targetId) {
            const selected = select.options[select.selectedIndex];
            const voyage = selected.getAttribute('data-voyage') || '';
            document.getElementById(targetId).value = voyage;
        }

        function updateVendorName(select, targetId) {
            const selected = select.options[select.selectedIndex];
            const name = selected.getAttribute('data-name') || '';
            document.getElementById(targetId).value = name;
        }
    </script>
</x-app-layout>
