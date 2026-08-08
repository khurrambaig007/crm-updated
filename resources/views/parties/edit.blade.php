<x-app-layout :title="'Edit PA Party'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 pr-10 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $radioClasses = 'h-4 w-4 rounded-full border-topbar-border text-primary-600 focus:ring-primary-500';
        $submitClasses = 'rounded-lg bg-primary-900 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $agentsData = $agents->mapWithKeys(fn ($agent) => [$agent->id => $agent->name]);
    @endphp

    <div class="mx-auto max-w-5xl space-y-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.building', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Edit PA Party</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Update the details for {{ $party->name }}.</p>
                </div>
            </div>
            <a href="{{ route('parties.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2.5 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                    <path d="m12 19-7-7 7-7" />
                    <path d="M19 12H5" />
                </svg>
                Back to PA parties
            </a>
        </div>

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
            {!! html()->model($party) !!}
            {!! html()->form('PATCH', route('parties.update', $party))->class('grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3')->open() !!}

                <div>
                    <label for="name" class="{{ $labelClasses }}">Name <span class="text-red-500">*</span></label>
                    {!! html()->text('name', $party->name)->class($inputClasses)->required() !!}
                    @error('name')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="code" class="{{ $labelClasses }}">Code <span class="text-red-500">*</span></label>
                    {!! html()->text('code', $party->code)->class($inputClasses)->required() !!}
                    @error('code')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Email Address', 'email')->class($labelClasses) !!}
                    {!! html()->email('email', $party->email)->class($inputClasses) !!}
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Phone Number', 'phone_No')->class($labelClasses) !!}
                    {!! html()->text('phone_No', $party->phone_No)->class($inputClasses)->attribute('placeholder', '03XX XXX XXXX')->attribute('data-phone-mask', '') !!}
                    @error('phone_No')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    {!! html()->label('Website', 'website')->class($labelClasses) !!}
                    {!! html()->text('website', $party->website)->class($inputClasses)->attribute('placeholder', 'https://example.com') !!}
                    @error('website')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="agent_id" class="{{ $labelClasses }}">Agent Code <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select name="agent_id" id="agent_id" data-agents='{{ $agentsData->toJson() }}' class="{{ $selectClasses }} appearance-none cursor-pointer">
                            <option value="">Select an agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('agent_id', $party->agent_id) == $agent->id ? 'selected' : '' }}>{{ $agent->code }}</option>
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
                    {!! html()->text('agent_name', '')->id('agent_name')->class($inputClasses . ' bg-gray-50 text-gray-500')->attribute('readonly', true)->attribute('placeholder', 'Select an agent code') !!}
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    {!! html()->label('Address', 'address')->class($labelClasses) !!}
                    {!! html()->textarea('address', $party->address)->class($inputClasses)->attribute('rows', 2) !!}
                    @error('address')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <span class="block text-sm font-medium text-topbar-text">Type <span class="text-red-500">*</span></span>
                    <div class="mt-3 flex flex-wrap gap-x-6 gap-y-2">
                        @foreach (['Customer', 'Vendor', 'Both'] as $option)
                            <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-topbar-text">
                                <input type="radio" name="type" value="{{ $option }}" {{ old('type', $party->type) === $option ? 'checked' : '' }} class="{{ $radioClasses }}">
                                {{ $option }}
                            </label>
                        @endforeach
                    </div>
                    @error('type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <span class="block text-sm font-medium text-topbar-text">Customer Type <span class="text-red-500">*</span></span>
                    <div class="mt-3 flex flex-wrap gap-x-6 gap-y-2">
                        @foreach (['Direct Customer', 'Traders'] as $option)
                            <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-topbar-text">
                                <input type="radio" name="line_type" value="{{ $option }}" {{ old('line_type', $party->line_type) === $option ? 'checked' : '' }} class="{{ $radioClasses }}">
                                {{ $option }}
                            </label>
                        @endforeach
                    </div>
                    @error('line_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="col-span-full flex items-center justify-between gap-3 pt-6">
                    <a href="{{ route('parties.index') }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
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
