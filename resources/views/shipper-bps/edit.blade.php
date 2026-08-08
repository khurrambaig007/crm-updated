<x-app-layout :title="'Edit Shipper / BP'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $readonlyClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-gray-50 px-3 py-2.5 text-gray-500 shadow-sm ring-1 ring-inset ring-card-border';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $submitClasses = 'rounded-lg bg-primary-900 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $agentsData = $agents->mapWithKeys(fn ($agent) => [$agent->id => $agent->name]);
        $portsData = $pols->mapWithKeys(fn ($pol) => [$pol->id => $pol->port_code]);
    @endphp

    <div class="mx-auto max-w-5xl space-y-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.package', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Edit Shipper / BP</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Update the details for {{ $shipperBp->name }}.</p>
                </div>
            </div>
            <a href="{{ route('shipper-bps.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>
                Back to shippers / BPs
            </a>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->form('PATCH', route('shipper-bps.update', $shipperBp))->class('grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3')->open() !!}

                <div>
                    <label for="code" class="{{ $labelClasses }}">Code</label>
                    {!! html()->text('code', $shipperBp->code)->id('code')->class($readonlyClasses)->attribute('readonly', true) !!}
                    <p class="mt-2 text-xs text-topbar-muted">Auto-generated and cannot be edited.</p>
                </div>

                <div>
                    <label for="agent_id" class="{{ $labelClasses }}">Agent Code <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="agent_id" id="agent_id" data-agents='{{ $agentsData->toJson() }}' class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select an agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('agent_id', $shipperBp->agent_id) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
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
                    {!! html()->label('Agent Name', 'agent_name')->class($labelClasses) !!}
                    {!! html()->text('agent_name', '')->id('agent_name')->class($readonlyClasses)->attribute('readonly', true)->attribute('placeholder', 'Select an agent code') !!}
                </div>

                <div>
                    <label for="name" class="{{ $labelClasses }}">Shipper Name <span class="text-red-500">*</span></label>
                    {!! html()->text('name', $shipperBp->name)->class($inputClasses)->required() !!}
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="port_id" class="{{ $labelClasses }}">Port <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="port_id" id="port_id" data-ports='{{ $portsData->toJson() }}' class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select a port</option>
                            @foreach ($pols as $pol)
                                <option value="{{ $pol->id }}" {{ old('port_id', $shipperBp->port_id) == $pol->id ? 'selected' : '' }}>{{ $pol->city }} ({{ $pol->port_code }})</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('port_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Port Code', 'port_code')->class($labelClasses) !!}
                    {!! html()->text('port_code', '')->id('port_code')->class($readonlyClasses)->attribute('readonly', true)->attribute('placeholder', 'Select a port') !!}
                </div>

                <div>
                    <label for="type" class="{{ $labelClasses }}">Type</label>
                    <div class="relative">
                        <select name="type" class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select a type</option>
                            @foreach (['Direct Party', 'Forwarder'] as $option)
                                <option value="{{ $option }}" {{ old('type', $shipperBp->type) === $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                                <path d="m6 9 6 6 6-6" />
                            </svg>
                        </div>
                    </div>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Tax ID', 'tax_id')->class($labelClasses) !!}
                    {!! html()->text('tax_id', $shipperBp->tax_id)->class($inputClasses) !!}
                    @error('tax_id')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Phone Number', 'phone_no')->class($labelClasses) !!}
                    {!! html()->text('phone_no', $shipperBp->phone_no)->class($inputClasses)->attribute('placeholder', '03XX XXX XXXX')->attribute('data-phone-mask', '') !!}
                    @error('phone_no')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Web', 'web')->class($labelClasses) !!}
                    {!! html()->text('web', $shipperBp->web)->class($inputClasses)->attribute('placeholder', 'https://example.com') !!}
                    @error('web')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Email Address', 'email')->class($labelClasses) !!}
                    {!! html()->email('email', $shipperBp->email)->class($inputClasses) !!}
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Fax', 'fax')->class($labelClasses) !!}
                    {!! html()->text('fax', $shipperBp->fax)->class($inputClasses) !!}
                    @error('fax')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    {!! html()->label('Address', 'address')->class($labelClasses) !!}
                    {!! html()->textarea('address', $shipperBp->address)->class($inputClasses . ' min-h-24')->attribute('rows', 2) !!}
                    @error('address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <span class="block text-sm font-medium text-topbar-text">Party Roles</span>
                    <div class="mt-3 flex flex-wrap gap-x-8 gap-y-3">
                        @foreach ([
                            'shipper' => 'Shipper',
                            'ca' => 'C/A',
                            'consignee' => 'Consignee',
                        ] as $key => $label)
                            <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-topbar-text">
                                <input type="checkbox" name="{{ $key }}" value="1" {{ old($key, $shipperBp->$key) ? 'checked' : '' }} class="h-4 w-4 rounded border-topbar-border text-primary-600 focus:ring-primary-500">
                                {{ $label }}
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="col-span-full flex items-center justify-between gap-3 pt-6">
                    <a href="{{ route('shipper-bps.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                            <path d="m12 19-7-7 7-7" />
                            <path d="M19 12H5" />
                        </svg>
                        Cancel
                    </a>
                    {!! html()->submit('Save changes')->class($submitClasses . ' w-auto px-6 py-2') !!}
                </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>
