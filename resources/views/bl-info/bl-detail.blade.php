<x-app-layout :title="$tabTitle">
    @php
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $selectClasses = 'mt-1 block w-full appearance-none rounded-md border-0 bg-card-bg px-2.5 py-1.5 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition cursor-pointer';
        $textareaClasses = 'mt-1 block w-full resize-y rounded-md border-0 bg-card-bg px-2.5 py-2 text-sm leading-relaxed text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $counterClasses = 'mt-1 block text-right text-[11px] tabular-nums text-topbar-muted';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $chevron = '<div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg></div>';

        // Section rails: first one sits under the card header, the rest hang off
        // a rule. pt-5 plus the form's space-y-5 keeps 20px on both sides.
        $rail = function (string $icon, string $heading, string $hint, bool $ruled = false): void {
            echo '<div class="flex items-center gap-3 '.($ruled ? 'border-t border-card-border pt-5' : '').'">';
            echo '<div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600">';
            echo view('components.icons.'.$icon, ['classes' => 'h-4 w-4']);
            echo '</div>';
            echo '<div>';
            echo '<h3 class="text-sm font-semibold text-topbar-text">'.e($heading).'</h3>';
            echo '<p class="text-xs text-topbar-muted">'.e($hint).'</p>';
            echo '</div></div>';
        };
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => 'Cargo particulars for bill of lading'])

        @include('bl-info._nav')

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md sm:p-8">
            <div class="mb-6 flex items-start gap-3">
                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    @include('components.icons.receipt', ['classes' => 'h-5 w-5'])
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-topbar-text">BL Detail</h2>
                    <p class="text-xs text-topbar-muted">Cargo particulars as they appear on the bill of lading</p>
                    <p class="mt-1 text-xs text-topbar-muted">
                        Shipper, consignee, routing and freight type are carried over from this booking. Change them here only if the B/L says otherwise.
                    </p>
                </div>
            </div>

            {!! html()->form('PATCH', route('bl-info.bl-detail.update', $booking))->id('bl-detail-form')->class('space-y-5')->open() !!}

            {{-- Section 1: Parties --}}
            @php($rail('users', 'Parties', 'Who is shipping the cargo and who is receiving it'))

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Shipper <span class="text-red-500">*</span>', 'bl_detail_shipper')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_detail_shipper" id="bl_detail_shipper" class="{{ $selectClasses }}" required>
                            <option value="">Select shipper</option>
                            @foreach ($shipperBps as $shipperBp)
                                <option value="{{ $shipperBp->id }}" {{ old('bl_detail_shipper', $blDetail->bl_detail_shipper) == $shipperBp->id ? 'selected' : '' }}>{{ $shipperBp->code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_detail_shipper')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Consignee <span class="text-red-500">*</span>', 'bl_detail_consignee')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_detail_consignee" id="bl_detail_consignee" class="{{ $selectClasses }}" required>
                            <option value="">Select consignee</option>
                            @foreach ($parties as $party)
                                <option value="{{ $party->id }}" {{ old('bl_detail_consignee', $blDetail->bl_detail_consignee) == $party->id ? 'selected' : '' }}>{{ $party->code }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_detail_consignee')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Notify Party 1', 'bl_detail_notify_party_1')->class($labelClasses) !!}
                    {!! html()->text('bl_detail_notify_party_1', old('bl_detail_notify_party_1', $blDetail->bl_detail_notify_party_1))->class($inputClasses)->placeholder('Company and address') !!}
                    @error('bl_detail_notify_party_1')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Notify Party 2', 'bl_detail_notify_party_2')->class($labelClasses) !!}
                    {!! html()->text('bl_detail_notify_party_2', old('bl_detail_notify_party_2', $blDetail->bl_detail_notify_party_2))->class($inputClasses)->placeholder('Company and address') !!}
                    @error('bl_detail_notify_party_2')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Section 2: Routing --}}
            @php($rail('ship', 'Routing', 'Where the cargo is loaded, carried and discharged', true))

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Port of Loading <span class="text-red-500">*</span>', 'bl_detail_pol')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_detail_pol" id="bl_detail_pol" class="{{ $selectClasses }}" required>
                            <option value="">Select port of loading</option>
                            @foreach ($pols as $pol)
                                <option value="{{ $pol->id }}" {{ old('bl_detail_pol', $blDetail->bl_detail_pol) == $pol->id ? 'selected' : '' }}>{{ $pol->city }} ({{ $pol->port_code }})</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_detail_pol')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Vessel / Voyage', 'bl_detail_vessel_voyage')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_detail_vessel_voyage" id="bl_detail_vessel_voyage" class="{{ $selectClasses }}">
                            <option value="">Select vessel / voyage</option>
                            @foreach ($vesselVoyages as $vesselVoyage)
                                <option value="{{ $vesselVoyage->id }}" {{ old('bl_detail_vessel_voyage', $blDetail->bl_detail_vessel_voyage) == $vesselVoyage->id ? 'selected' : '' }}>{{ $vesselVoyage->vessel_name }} ({{ $vesselVoyage->voyage_number }})</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_detail_vessel_voyage')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Port of Discharge <span class="text-red-500">*</span>', 'bl_detail_pofd')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_detail_pofd" id="bl_detail_pofd" class="{{ $selectClasses }}" required>
                            <option value="">Select port of discharge</option>
                            @foreach ($pofds as $pod)
                                <option value="{{ $pod->id }}" {{ old('bl_detail_pofd', $blDetail->bl_detail_pofd) == $pod->id ? 'selected' : '' }}>{{ $pod->city }} ({{ $pod->location_code }})</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_detail_pofd')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Place of Delivery', 'bl_detail_place_of_delivery')->class($labelClasses) !!}
                    {!! html()->text('bl_detail_place_of_delivery', old('bl_detail_place_of_delivery', $blDetail->bl_detail_place_of_delivery))->class($inputClasses)->placeholder('Warehouse or inland destination') !!}
                    @error('bl_detail_place_of_delivery')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Section 3: Freight and delivery terms --}}
            @php($rail('package', 'Freight and delivery terms', 'Originals issued, how weight is stated and who releases the cargo', true))

            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label("No. of Original B/L's", 'bl_detail_original_bls')->class($labelClasses) !!}
                    {!! html()->text('bl_detail_original_bls', old('bl_detail_original_bls', $blDetail->bl_detail_original_bls))->class($inputClasses)->placeholder('e.g. 3') !!}
                    @error('bl_detail_original_bls')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Units', 'bl_detail_units')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_detail_units" id="bl_detail_units" class="{{ $selectClasses }}">
                            <option value="">Select unit</option>
                            @foreach ($units as $unitValue => $unitLabel)
                                <option value="{{ $unitValue }}" data-hint="Gross weight is stated in {{ $unitLabel }}." {{ old('bl_detail_units', $blDetail->bl_detail_units) == $unitValue ? 'selected' : '' }}>{{ $unitLabel }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    <p data-unit-hint class="mt-1 text-[11px] text-topbar-muted"></p>
                    @error('bl_detail_units')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Freight Type', 'bl_detail_freight_type')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_detail_freight_type" id="bl_detail_freight_type" class="{{ $selectClasses }}">
                            <option value="">Select freight type</option>
                            @foreach ($freightTypes as $freightValue => $freightLabel)
                                <option value="{{ $freightValue }}" {{ old('bl_detail_freight_type', $blDetail->bl_detail_freight_type) == $freightValue ? 'selected' : '' }}>{{ $freightLabel }}</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_detail_freight_type')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Delivery Agent', 'bl_detail_delivery_agent')->class($labelClasses) !!}
                    <div class="relative">
                        <select name="bl_detail_delivery_agent" id="bl_detail_delivery_agent" class="{{ $selectClasses }}">
                            <option value="">Select delivery agent</option>
                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}" {{ old('bl_detail_delivery_agent', $blDetail->bl_detail_delivery_agent) == $agent->id ? 'selected' : '' }}>{{ $agent->name }} ({{ $agent->code }})</option>
                            @endforeach
                        </select>
                        {!! $chevron !!}
                    </div>
                    @error('bl_detail_delivery_agent')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Section 4 + 5: Cargo particulars and issuance, recessed like the ruled block on the printed B/L --}}
            @php($rail('layers', 'Cargo particulars and issuance', 'Transcribe the particulars block exactly as printed', true))

            <div class="space-y-5 rounded-xl bg-page-bg p-4 ring-1 ring-card-border sm:p-5">
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('Marks &amp; No. Container No\'s', 'bl_detail_marks_and_numbers')->class($labelClasses) !!}
                        {!! html()->textarea('bl_detail_marks_and_numbers', old('bl_detail_marks_and_numbers', $blDetail->bl_detail_marks_and_numbers))->class($textareaClasses)->rows(5)->placeholder('Shipping marks, container and seal numbers')->attribute('maxlength', 2000)->attribute('data-counter', 'true') !!}
                        <span data-counter-for="bl_detail_marks_and_numbers" class="{{ $counterClasses }}"></span>
                        @error('bl_detail_marks_and_numbers')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        {!! html()->label('No. of Pckgs or Shipping Units', 'bl_detail_no_of_packages')->class($labelClasses) !!}
                        {!! html()->textarea('bl_detail_no_of_packages', old('bl_detail_no_of_packages', $blDetail->bl_detail_no_of_packages))->class($textareaClasses)->rows(5)->placeholder('e.g. 240 cartons, 18 pallets')->attribute('maxlength', 1000)->attribute('data-counter', 'true') !!}
                        <span data-counter-for="bl_detail_no_of_packages" class="{{ $counterClasses }}"></span>
                        @error('bl_detail_no_of_packages')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        {!! html()->label('Description of Goods &amp; Pkgs', 'bl_detail_description_of_goods')->class($labelClasses) !!}
                        {!! html()->textarea('bl_detail_description_of_goods', old('bl_detail_description_of_goods', $blDetail->bl_detail_description_of_goods))->class($textareaClasses)->rows(5)->placeholder('Commodity and packaging as declared')->attribute('maxlength', 2000)->attribute('data-counter', 'true') !!}
                        <span data-counter-for="bl_detail_description_of_goods" class="{{ $counterClasses }}"></span>
                        @error('bl_detail_description_of_goods')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        {!! html()->label('Gross Weight', 'bl_detail_gross_weight')->class($labelClasses) !!}
                        {!! html()->textarea('bl_detail_gross_weight', old('bl_detail_gross_weight', $blDetail->bl_detail_gross_weight))->class($textareaClasses)->rows(5)->placeholder('e.g. 18,400')->attribute('maxlength', 1000)->attribute('data-counter', 'true') !!}
                        <span data-counter-for="bl_detail_gross_weight" class="{{ $counterClasses }}"></span>
                        @error('bl_detail_gross_weight')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        {!! html()->label('On Board Date', 'bl_detail_on_board_date')->class($labelClasses) !!}
                        {!! html()->date('bl_detail_on_board_date', old('bl_detail_on_board_date', $blDetail->bl_detail_on_board_date))->class($inputClasses) !!}
                        @error('bl_detail_on_board_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        {!! html()->label('Place of Issue', 'bl_detail_place_of_issue')->class($labelClasses) !!}
                        {!! html()->text('bl_detail_place_of_issue', old('bl_detail_place_of_issue', $blDetail->bl_detail_place_of_issue))->class($inputClasses)->placeholder('City where the B/L is issued') !!}
                        @error('bl_detail_place_of_issue')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        {!! html()->label('Date of Issue <span class="text-red-500">*</span>', 'bl_detail_date_of_issue')->class($labelClasses) !!}
                        {!! html()->date('bl_detail_date_of_issue', old('bl_detail_date_of_issue', $blDetail->bl_detail_date_of_issue))->class($inputClasses)->required() !!}
                        @error('bl_detail_date_of_issue')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        {!! html()->label('&nbsp;', 'bl_detail_issuance_empty')->class($labelClasses) !!}
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    <div class="lg:col-span-4">
                        {!! html()->label('Measurement', 'bl_detail_measurement')->class($labelClasses) !!}
                        {!! html()->textarea('bl_detail_measurement', old('bl_detail_measurement', $blDetail->bl_detail_measurement))->class($textareaClasses)->rows(3)->placeholder('CBM and other dimensions')->attribute('maxlength', 1000)->attribute('data-counter', 'true') !!}
                        <span data-counter-for="bl_detail_measurement" class="{{ $counterClasses }}"></span>
                        @error('bl_detail_measurement')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 pt-2">
                <a href="{{ route('bl-info.bl-detail.pdf-view', $booking) }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-primary-900 ring-1 ring-inset ring-card-border transition-colors hover:bg-primary-50">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                    View PDF
                </a>
                <a href="{{ route('bl-info.bl-detail.pdf', $booking) }}" class="inline-flex items-center gap-2 rounded-lg bg-white px-4 py-2 text-sm font-semibold text-primary-900 ring-1 ring-inset ring-card-border transition-colors hover:bg-primary-50">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                        <polyline points="7 10 12 15 17 10" />
                        <line x1="12" x2="12" y1="15" y2="3" />
                    </svg>
                    Download PDF
                </a>
                <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">Cancel</a>
                {!! html()->submit('Save BL Detail')->class($submitClasses) !!}
            </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>