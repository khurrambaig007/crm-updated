<x-app-layout :title="$tabTitle">
    @php
        $labelClasses = 'block text-xs font-medium text-topbar-text';
        $inputClasses = 'mt-1 block w-full rounded-md border-0 bg-card-bg px-2.5 py-1.5 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => 'External references for this bill of lading'])

        @include('bl-info._nav')

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md sm:p-8">
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                    @include('components.icons.tag', ['classes' => 'h-5 w-5'])
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-topbar-text">Ref NO's</h2>
                    <p class="text-xs text-topbar-muted">Invoice, export, contact and letter-of-credit references, with the date each was issued</p>
                </div>
            </div>

            {!! html()->form('PATCH', route('bl-info.refnos.update', $booking))->id('refnos-form')->class('space-y-5')->open() !!}

            {{-- Row 1: Invoice# · Invoice Date · Export# · Export Date --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Invoice#', 'refno_invoice_no')->class($labelClasses) !!}
                    {!! html()->text('refno_invoice_no', old('refno_invoice_no', $blDetail->refno_invoice_no))->class($inputClasses)->placeholder('Enter invoice number') !!}
                    @error('refno_invoice_no')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Invoice Date', 'refno_invoice_date')->class($labelClasses) !!}
                    {!! html()->date('refno_invoice_date', old('refno_invoice_date', $blDetail->refno_invoice_date))->class($inputClasses) !!}
                    @error('refno_invoice_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Export#', 'refno_export_no')->class($labelClasses) !!}
                    {!! html()->text('refno_export_no', old('refno_export_no', $blDetail->refno_export_no))->class($inputClasses)->placeholder('Enter export number') !!}
                    @error('refno_export_no')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Export Date', 'refno_export_date')->class($labelClasses) !!}
                    {!! html()->date('refno_export_date', old('refno_export_date', $blDetail->refno_export_date))->class($inputClasses) !!}
                    @error('refno_export_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Row 2: Contact# · Contact Date · L/C# · L/C Date --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Contact#', 'refno_contact_no')->class($labelClasses) !!}
                    {!! html()->text('refno_contact_no', old('refno_contact_no', $blDetail->refno_contact_no))->class($inputClasses)->placeholder('Enter contact number') !!}
                    @error('refno_contact_no')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Contact Date', 'refno_contact_date')->class($labelClasses) !!}
                    {!! html()->date('refno_contact_date', old('refno_contact_date', $blDetail->refno_contact_date))->class($inputClasses) !!}
                    @error('refno_contact_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('L/C#', 'refno_lc_no')->class($labelClasses) !!}
                    {!! html()->text('refno_lc_no', old('refno_lc_no', $blDetail->refno_lc_no))->class($inputClasses)->placeholder('Enter letter of credit number') !!}
                    @error('refno_lc_no')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('L/C Date', 'refno_lc_date')->class($labelClasses) !!}
                    {!! html()->date('refno_lc_date', old('refno_lc_date', $blDetail->refno_lc_date))->class($inputClasses) !!}
                    @error('refno_lc_date')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Row 3: Shipper Ref# · Client Ref#, seeded from the booking's reference --}}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    {!! html()->label('Shipper Ref#', 'refno_shipper_ref')->class($labelClasses) !!}
                    {!! html()->text('refno_shipper_ref', old('refno_shipper_ref', $blDetail->refno_shipper_ref))->class($inputClasses)->placeholder('Enter shipper reference') !!}
                    @error('refno_shipper_ref')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('Client Ref#', 'refno_client_ref')->class($labelClasses) !!}
                    {!! html()->text('refno_client_ref', old('refno_client_ref', $blDetail->refno_client_ref))->class($inputClasses)->placeholder('Enter client reference') !!}
                    @error('refno_client_ref')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    {!! html()->label('&nbsp;', 'refno_empty_1')->class($labelClasses) !!}
                </div>

                <div>
                    {!! html()->label('&nbsp;', 'refno_empty_2')->class($labelClasses) !!}
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">Cancel</a>
                {!! html()->submit('Save Ref NO\'s')->class($submitClasses) !!}
            </div>

            {!! html()->form()->close() !!}
        </div>
    </div>
</x-app-layout>