<x-app-layout :title="'Split Booking'">
    @php
        $labelClasses = 'block text-sm font-medium text-topbar-text';
        $inputClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-card-bg px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border placeholder:text-topbar-muted focus:ring-2 focus:ring-inset focus:ring-primary-500 transition';
        $readOnlyClasses = 'mt-1.5 block w-full rounded-lg border-0 bg-gray-100 px-3 py-2.5 text-topbar-muted shadow-sm ring-1 ring-inset ring-card-border cursor-not-allowed';
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 hover:shadow-md hover:-translate-y-0.5 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-900 transition-all duration-200';
        $cancelClasses = 'inline-flex items-center justify-center rounded-lg px-4 py-2.5 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text';
    @endphp

    <div class="mx-auto max-w-full space-y-8" data-booking-split data-next-booking-no="{{ $splitBookingNo }}">
        {{-- Page header sits outside the form: it holds a link, not a control. --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                @include('components.icons.calendar', ['classes' => 'h-7 w-7 text-primary-600'])
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight text-topbar-text">Split Booking</h1>
                    <p class="mt-1 text-sm text-topbar-muted">Move selected equipment into a new child booking.</p>
                </div>
            </div>
            <a href="{{ route('bookings.edit', $booking) }}" class="{{ $cancelClasses }}">Cancel</a>
        </div>

        {{-- space-y-8 belongs on the form itself: the cards below are its children. --}}
        {!! html()->form('POST', route('bookings.split.store', $booking))->id('split-form')->class('space-y-8')->open() !!}

            {{-- Source booking --}}
            <div class="rounded-2xl bg-card-bg shadow-lg ring-1 ring-card-border">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4 sm:px-8">
                    <h2 class="text-lg font-semibold text-gray-800">Source Booking</h2>
                </div>
                <div class="space-y-5 p-6 sm:p-8">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            {!! html()->label('Booking #', 'split-booking-no')->class($labelClasses) !!}
                            <input type="text" id="split-booking-no" class="{{ $readOnlyClasses }}" value="{{ $booking->booking_no }}" readonly>
                        </div>
                        <div>
                            {!! html()->label('New Booking #', 'split-new-booking-no')->class($labelClasses) !!}
                            <input type="text" id="split-new-booking-no" class="{{ $readOnlyClasses }}" value="{{ $splitBookingNo }}" readonly>
                        </div>
                        <div>
                            {!! html()->label('Reference #', 'split-reference-no')->class($labelClasses) !!}
                            <input type="text" id="split-reference-no" class="{{ $readOnlyClasses }}" value="{{ $booking->reference_no }}" readonly>
                        </div>
                        <div>
                            {!! html()->label('Reporting #', 'split-reporting-no')->class($labelClasses) !!}
                            <input type="text" id="split-reporting-no" class="{{ $readOnlyClasses }}" value="{{ $booking->reporting_no }}" readonly>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            {!! html()->label('POL', 'split-pol')->class($labelClasses) !!}
                            <input type="text" id="split-pol" class="{{ $readOnlyClasses }}" value="{{ $booking->polPol?->city ?? '—' }}" readonly>
                        </div>
                        <div>
                            {!! html()->label('POFD', 'split-pofd')->class($labelClasses) !!}
                            <input type="text" id="split-pofd" class="{{ $readOnlyClasses }}" value="{{ $booking->podPofd?->city ?? '—' }}" readonly>
                        </div>
                        <div>
                            {!! html()->label('Sailing Date', 'split-sailing-date')->class($labelClasses) !!}
                            <input type="text" id="split-sailing-date" class="{{ $readOnlyClasses }}" value="{{ $booking->sailing_date?->format('M j, Y') }}" readonly>
                        </div>
                        <div>
                            {!! html()->label('Carrier', 'split-carrier')->class($labelClasses) !!}
                            <input type="text" id="split-carrier" class="{{ $readOnlyClasses }}" value="{{ $booking->carrier?->name ?? '—' }}" readonly>
                        </div>
                    </div>

                    @if ($booking->parentBooking)
                        <div class="rounded-lg bg-blue-50 px-4 py-3 text-sm text-blue-700 ring-1 ring-blue-200">
                            This booking is a split of
                            <a href="{{ route('bookings.edit', $booking->parentBooking) }}" class="font-semibold underline">{{ $booking->parentBooking->booking_no }}</a>.
                            Splitting it again creates {{ $splitBookingNo }}.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Equipment selection --}}
            <div class="rounded-2xl bg-card-bg shadow-lg ring-1 ring-card-border">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4 sm:px-8">
                    <h2 class="text-lg font-semibold text-gray-800">Select Equipment</h2>
                </div>

                <div class="space-y-5 p-6 sm:p-8">
                    @if ($equipmentGroups->isEmpty())
                        <p class="text-sm text-topbar-muted">
                            This booking has no equipment to split. Add equipment to the booking first.
                        </p>
                    @else
                        @if ($errors->has('equipment'))
                            <div class="rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200">
                                {{ $errors->first('equipment') }}
                            </div>
                        @endif

                        <div class="overflow-hidden rounded-xl ring-1 ring-card-border">
                            <table class="min-w-full divide-y divide-gray-200 text-sm" data-split-equipment-table>
                                <thead class="bg-primary-900 text-white">
                                    <tr>
                                        <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">Equipment Type</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Original</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Split Quantity</th>
                                        <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-card-bg">
                                    @foreach ($equipmentGroups as $index => $group)
                                        <tr data-split-row data-split-group="{{ $index }}" data-max="{{ $group['quantity'] }}">
                                            <td class="px-4 py-3">
                                                <span class="font-medium text-topbar-text">{{ $group['size_label'] }}</span>
                                                <span class="mx-1.5 text-topbar-muted" aria-hidden="true">&middot;</span>
                                                <span class="text-topbar-muted">{{ $group['type_label'] }}</span>
                                                <input type="hidden" name="equipment[{{ $index }}][size]" value="{{ $group['size'] }}">
                                                <input type="hidden" name="equipment[{{ $index }}][type]" value="{{ $group['type'] }}">
                                            </td>
                                            <td class="px-4 py-3 text-right tabular-nums text-topbar-text">{{ $group['quantity'] }}</td>
                                            <td class="px-4 py-3 text-right">
                                                <input type="number"
                                                    name="equipment[{{ $index }}][quantity]"
                                                    id="split-quantity-{{ $index }}"
                                                    class="{{ $inputClasses }} text-right"
                                                    value="{{ old("equipment.$index.quantity", 0) }}"
                                                    min="0"
                                                    max="{{ $group['quantity'] }}"
                                                    step="any"
                                                    data-split-quantity
                                                    aria-label="Split quantity for {{ $group['size_label'] }} {{ $group['type_label'] }}">
                                            </td>
                                            <td class="px-4 py-3 text-right tabular-nums text-topbar-text" data-split-remaining>{{ $group['quantity'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <p class="text-sm text-topbar-muted" data-split-summary>
                            Select how much equipment to move to the new booking. Revenue and cost lines stay on the original booking.
                        </p>
                    @endif
                </div>
            </div>

            {{-- Child booking equipment details: one 4-column grid per equipment group --}}
            @unless ($equipmentGroups->isEmpty())
                <div class="rounded-2xl bg-card-bg shadow-lg ring-1 ring-card-border">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4 sm:px-8">
                        <h2 class="text-lg font-semibold text-gray-800">New Booking Details</h2>
                    </div>

                    <div class="space-y-6 p-6 sm:p-8">
                        <p class="text-sm text-topbar-muted">
                            These fields pre-fill pro-rata from {{ $booking->booking_no }} as you enter quantities above.
                            Override any of them when the split is not perfectly even.
                        </p>

                        @foreach ($equipmentGroups as $index => $group)
                            @php
                                // Group totals the split JS pro-rates against the entered
                                // quantity. The fields start empty because the split
                                // quantity starts at 0.
                                $groupRows = $group['rows'];
                                $grossWeight = $groupRows->sum(fn ($e) => (float) $e->gross_weight);
                                $packages = $groupRows->sum(fn ($e) => (float) $e->packages);
                                $cargoVolumn = $groupRows->sum(fn ($e) => (float) $e->cargo_volumn);
                                $unit = $groupRows->first()->unit;
                                $approvalStatus = $groupRows->first()->approval_status;
                            @endphp

                            <div class="space-y-3">
                                <div class="flex flex-wrap items-baseline gap-x-2">
                                    <h3 class="text-sm font-semibold text-topbar-text">
                                        {{ $group['size_label'] }} <span class="mx-1 text-topbar-muted" aria-hidden="true">&middot;</span> {{ $group['type_label'] }}
                                    </h3>
                                    <p class="text-sm text-topbar-muted">
                                        <span class="font-medium tabular-nums text-topbar-text" data-split-detail-quantity="{{ $index }}">0</span>
                                        of {{ $group['quantity'] }} moving
                                    </p>
                                </div>

                                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                                    <div>
                                        {!! html()->label('Gross Weight', "split-gross-weight-{$index}")->class($labelClasses) !!}
                                        <input type="text"
                                            name="equipment[{{ $index }}][gross_weight]"
                                            id="split-gross-weight-{{ $index }}"
                                            class="{{ $inputClasses }}"
                                            value="{{ old("equipment.$index.gross_weight") }}"
                                            data-split-pro-rata="{{ $grossWeight }}"
                                            data-split-group="{{ $index }}"
                                            placeholder="0.00">
                                    </div>
                                    <div>
                                        {!! html()->label('Packages', "split-packages-{$index}")->class($labelClasses) !!}
                                        <input type="text"
                                            name="equipment[{{ $index }}][packages]"
                                            id="split-packages-{{ $index }}"
                                            class="{{ $inputClasses }}"
                                            value="{{ old("equipment.$index.packages") }}"
                                            data-split-pro-rata="{{ $packages }}"
                                            data-split-group="{{ $index }}"
                                            placeholder="0.00">
                                    </div>
                                    <div>
                                        {!! html()->label('Cargo Volume', "split-cargo-volumn-{$index}")->class($labelClasses) !!}
                                        <input type="text"
                                            name="equipment[{{ $index }}][cargo_volumn]"
                                            id="split-cargo-volumn-{{ $index }}"
                                            class="{{ $inputClasses }}"
                                            value="{{ old("equipment.$index.cargo_volumn") }}"
                                            data-split-pro-rata="{{ $cargoVolumn }}"
                                            data-split-group="{{ $index }}"
                                            placeholder="0.00">
                                    </div>
                                    <div>
                                        {!! html()->label('Unit', "split-unit-{$index}")->class($labelClasses) !!}
                                        <input type="text"
                                            name="equipment[{{ $index }}][unit]"
                                            id="split-unit-{{ $index }}"
                                            class="{{ $inputClasses }}"
                                            value="{{ old("equipment.$index.unit", $unit) }}"
                                            data-split-group="{{ $index }}"
                                            placeholder="—">
                                    </div>
                                </div>

                                <input type="hidden" name="equipment[{{ $index }}][approval_status]" value="{{ $approvalStatus }}">
                            </div>
                        @endforeach
                    </div>
                </div>
            @endunless

            {{-- Actions live in their own card so they read as part of the form --}}
            @unless ($equipmentGroups->isEmpty())
                <div class="rounded-2xl bg-card-bg shadow-lg ring-1 ring-card-border">
                    <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-end sm:p-8">
                        <a href="{{ route('bookings.edit', $booking) }}" class="{{ $cancelClasses }}">Cancel</a>
                        <button type="submit" id="split-submit" class="{{ $submitClasses }}" data-split-submit disabled>
                            Create Split Booking
                        </button>
                    </div>
                </div>
            @endunless

        {!! html()->form()->close() !!}
    </div>
</x-app-layout>
