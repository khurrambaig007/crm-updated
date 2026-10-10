<x-app-layout :title="$tabTitle">
    @php
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full appearance-none rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition cursor-pointer';
        $textareaClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $lockedClasses = 'mt-1 block w-full rounded-md border-0 bg-gray-100 px-2.5 py-1.5 text-sm text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $chevron = '<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg></div>';
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => 'Bill of lading header for booking'])

        @include('bl-info._nav')

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    @include('components.icons.truck', ['classes' => 'h-5 w-5'])
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-topbar-text">Delivery Order</h2>
                    <p class="text-xs text-topbar-muted">Delivery order details and the party the cargo is released to</p>
                </div>
            </div>

            {!! html()->form('PATCH', route('bl-info.delivery-order.update', $booking))->id('delivery-order-form')->class('space-y-5')->open() !!}

            {{-- Row 1: Doc# · Date · Validity Date --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Doc#', 'delivery_order_doc')->class($labelClasses) !!}
                    {!! html()->text('delivery_order_doc', old('delivery_order_doc', $blDetail->delivery_order_doc))->class($inputClasses)->placeholder('Enter document number') !!}
                    @error('delivery_order_doc')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Date <span class="text-red-500">*</span>', 'delivery_order_date')->class($labelClasses) !!}
                    {!! html()->date('delivery_order_date', old('delivery_order_date', $blDetail->delivery_order_date))->class($inputClasses)->required() !!}
                    @error('delivery_order_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Validity Date', 'delivery_order_validity_date')->class($labelClasses) !!}
                    {!! html()->date('delivery_order_validity_date', old('delivery_order_validity_date', $blDetail->delivery_order_validity_date))->class($inputClasses) !!}
                    @error('delivery_order_validity_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('&nbsp;', 'delivery_order_empty_1')->class($labelClasses) !!}
                </div>
            </div>

            {{-- Row 2: Agent · Agent Name · Vessel/Voyage · Voyage No. --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Agent', 'delivery_order_agent')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="delivery_order_agent" id="delivery_order_agent" class="{{ $selectClasses }}">
                            <option value="">Select agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" data-detail="{{ $agent->name }}" {{ old('delivery_order_agent', $blDetail->delivery_order_agent ?? $booking->agent_pol) == $agent->id ? 'selected' : '' }}>{{ $agent->code ? '('.$agent->code.') '.$agent->name : $agent->name }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('delivery_order_agent')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Agent Name', 'delivery_order_agent_detail')->class($labelClasses) !!}
                    <input type="text" id="delivery_order_agent_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from agent" disabled>
                </div>

                <div>
                    {!! html()->label('Vessel / Voyage', 'delivery_order_vessel_voyage')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="delivery_order_vessel_voyage" id="delivery_order_vessel_voyage" class="{{ $selectClasses }}">
                            <option value="">Select vessel / voyage</option>
                            @foreach ($vesselVoyages as $vesselVoyage)
                                <option value="{{ $vesselVoyage->id }}" data-detail="{{ $vesselVoyage->voyage_number }}" {{ old('delivery_order_vessel_voyage', $blDetail->delivery_order_vessel_voyage ?? $booking->vessel_voyage) == $vesselVoyage->id ? 'selected' : '' }}>{{ $vesselVoyage->vessel_name }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('delivery_order_vessel_voyage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Voyage No.', 'delivery_order_vessel_voyage_detail')->class($labelClasses) !!}
                    <input type="text" id="delivery_order_vessel_voyage_detail" class="{{ $lockedClasses }}" placeholder="Autofilled from voyage" disabled>
                </div>
            </div>

            {{-- Row 3: Deliver To · Remarks --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    {!! html()->label('Deliver To', 'delivery_order_deliver_to')->class($labelClasses) !!}
                    {!! html()->textarea('delivery_order_deliver_to', old('delivery_order_deliver_to', $blDetail->delivery_order_deliver_to))->class($textareaClasses)->rows(3)->placeholder('Enter delivery details...') !!}
                    @error('delivery_order_deliver_to')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="lg:col-span-2">
                    {!! html()->label('Remarks', 'delivery_order_remarks')->class($labelClasses) !!}
                    {!! html()->textarea('delivery_order_remarks', old('delivery_order_remarks', $blDetail->delivery_order_remarks))->class($textareaClasses)->rows(3)->placeholder('Enter remarks...') !!}
                    @error('delivery_order_remarks')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">Cancel</a>
                {!! html()->submit('Save Delivery Order')->class($submitClasses) !!}
            </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>