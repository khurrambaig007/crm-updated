<div class="mt-6 space-y-5">
    <div class="mb-8 border-b border-gray-200">
        <ul class="flex flex-wrap -mb-px text-sm font-medium text-center text-gray-500">
            <li class="mr-2">
                <a href="#equipments" class="sub-tab-link inline-block p-4 border-b-2 border-primary-600 text-primary-600 rounded-t-lg active">Equipments</a>
            </li>
            <li class="mr-2">
                <a href="#revenue" class="sub-tab-link inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">Revenue</a>
            </li>
            <li class="mr-2">
                <a href="#cost" class="sub-tab-link inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg">Cost</a>
            </li>
        </ul>
    </div>

    <div id="equipments" class="sub-tab-pane space-y-5">
        @include('bookings._equipment')
    </div>

    <div id="revenue" class="sub-tab-pane hidden space-y-5">
        @include('bookings._revenue')
    </div>

    <div id="cost" class="sub-tab-pane hidden space-y-5">
        @include('bookings._cost')
    </div>

    @php
        $revenueTotal = (float) $booking->revenues->sum('amount');
        $costTotal = (float) $booking->costs->sum('amount');
        $netTotal = $revenueTotal - $costTotal;
        $fmt = fn ($n) => number_format((float) $n, 2);
        $canApprove = auth()->user()->isSuperAdmin() || auth()->user()->can('bookings.edit');
    @endphp

    <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border sm:p-8">
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                {!! html()->label('Revenue', 'booking-revenue-total')->class('block text-sm font-medium text-topbar-text') !!}
                {!! html()->text('booking_revenue_total', $fmt($revenueTotal))->id('booking-revenue-total')->class('mt-1.5 block w-full rounded-lg border-0 bg-topbar-muted/5 px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition')->attribute('readonly', 'readonly') !!}
            </div>
            <div>
                {!! html()->label('Cost', 'booking-cost-total')->class('block text-sm font-medium text-topbar-text') !!}
                {!! html()->text('booking_cost_total', $fmt($costTotal))->id('booking-cost-total')->class('mt-1.5 block w-full rounded-lg border-0 bg-topbar-muted/5 px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition')->attribute('readonly', 'readonly') !!}
            </div>
            <div>
                {!! html()->label('Net', 'booking-net-total')->class('block text-sm font-medium text-topbar-text') !!}
                {!! html()->text('booking_net_total', $fmt($netTotal))->id('booking-net-total')->class('mt-1.5 block w-full rounded-lg border-0 bg-topbar-muted/5 px-3 py-2.5 text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500 transition')->attribute('readonly', 'readonly') !!}
            </div>
            <div class="flex items-end">
                @if ($canApprove)
                    <button type="button" id="booking-approve-btn" data-booking-id="{{ $booking->id }}" data-approved="{{ $booking->approved ? 1 : 0 }}"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold shadow-lg transition-all {{ $booking->approved ? 'bg-emerald-600 text-white shadow-emerald-600/25 hover:bg-emerald-700' : 'bg-primary-600 text-white shadow-primary-600/25 hover:bg-primary-700' }}">
                        {{ $booking->approved ? 'Approved' : 'Approve' }}
                    </button>
                @else
                    <span class="inline-flex w-full items-center justify-center rounded-xl bg-topbar-muted/10 px-5 py-2.5 text-sm font-semibold text-topbar-muted">
                        {{ $booking->approved ? 'Approved' : 'Pending' }}
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
