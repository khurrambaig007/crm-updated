<style>
    #costs-grid.ag-theme-quartz {
        --ag-row-height: 44px !important;
        --ag-cell-horizontal-padding: 10px;
        --ag-font-size: 13px;
        --ag-font-family: inherit;
        --ag-background-color: var(--color-card-bg);
        --ag-odd-row-background-color: color-mix(in srgb, var(--color-primary-50) 40%, transparent);
        --ag-row-hover-color: var(--color-primary-50);
        --ag-border-color: var(--color-card-border);
        --ag-header-background-color: var(--color-primary-900);
        --ag-header-foreground-color: #ffffff;
        --ag-header-column-resize-handle-color: rgba(255, 255, 255, 0.4);
        --ag-header-cell-hover-background-color: var(--color-primary-700);
        --ag-header-cell-moving-background-color: var(--color-primary-800);
        --ag-selected-row-background-color: var(--color-primary-100);
    }
</style>
<div class="space-y-5">
    {{-- Row 1: Cost input row --}}
    <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            {!! html()->label('Charges', 'cs-charges')->class($labelClasses) !!}
            <div class="relative">
                <select name="cs_charges" id="cs-charges" class="{{ $selectClasses }}">
                    <option value="">Select Charge</option>
                    @foreach ($charges as $charge)
                        <option value="{{ $charge->id }}">{{ $charge->name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
        </div>
        <div>
            {!! html()->label('Size', 'cs-size')->class($labelClasses) !!}
            <div class="relative">
                <select name="cs_size" id="cs-size" class="{{ $selectClasses }}">
                    <option value="">Select Size</option>
                    @foreach ($containerSizes as $containerSize)
                        <option value="{{ $containerSize->id }}">{{ $containerSize->size }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
        </div>
        <div>
            {!! html()->label('Type', 'cs-type')->class($labelClasses) !!}
            <div class="relative">
                <select name="cs_type" id="cs-type" class="{{ $selectClasses }}">
                    <option value="">Select Type</option>
                    @foreach ($containerTypes as $containerType)
                        <option value="{{ $containerType->id }}">{{ $containerType->name }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
        </div>
        <div>
            {!! html()->label('Quantity', 'cs-quantity')->class($labelClasses) !!}
            {!! html()->number('cs_quantity', old('cs_quantity'))->id('cs-quantity')->class($inputClasses)->attribute('step', 'any')->attribute('min', '0') !!}
        </div>
    </div>

    <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            {!! html()->label('Mrg', 'cs-mrg')->class($labelClasses) !!}
            {!! html()->number('cs_mrg', old('cs_mrg'))->id('cs-mrg')->class($inputClasses)->attribute('step', 'any') !!}
        </div>
        <div>
            {!! html()->label('Cost', 'cs-cost')->class($labelClasses) !!}
            {!! html()->number('cs_cost', old('cs_cost'))->id('cs-cost')->class($inputClasses)->attribute('step', 'any') !!}
        </div>
        <div>
            {!! html()->label('Currency', 'cs-currency')->class($labelClasses) !!}
            <div class="relative">
                <select name="cs_currency" id="cs-currency" class="{{ $selectClasses }}">
                    <option value="">Select Currency</option>
                    @foreach ($currencies as $currencyCode => $currencyLabel)
                        <option value="{{ $currencyCode }}">{{ $currencyLabel }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
        </div>
        <div>
            {!! html()->label('Ex. Range', 'cs-ex-rate')->class($labelClasses) !!}
            {!! html()->number('cs_ex_rate', old('cs_ex_rate', '1'))->id('cs-ex-rate')->class($inputClasses)->attribute('step', 'any') !!}
        </div>
    </div>

    <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div></div>
        <div></div>
        <div></div>
        <div>
            {!! html()->label('&nbsp;', 'cs-actions')->class($labelClasses) !!}
            <div class="flex items-center gap-2">
                <button type="button" id="cs-add-row" title="Add Row" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-emerald-600 text-white shadow-sm hover:bg-emerald-500 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                </button>
                <button type="button" id="cs-clear-form" title="Clear Form" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-500 text-white shadow-sm hover:bg-red-400 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                </button>
            </div>
        </div>
    </div>

    <p class="text-xs text-topbar-muted">Exchange rates as of {{ $exchangeRateDate ?? 'N/A' }}</p>

    {{-- Row 3: agGrid --}}
    <div id="costs-grid" class="ag-theme-quartz overflow-hidden rounded-xl ring-1 ring-card-border shadow-sm" style="width: 100%; height: 320px;"></div>

    @php
        $costRows = $booking->costs->map(function ($c) {
            return [
                'id' => $c->id,
                'charges' => $c->charge?->name,
                'size' => $c->containerSize?->size,
                'type' => $c->containerType?->name,
                'quantity' => $c->quantity,
                'mrg' => $c->mrg,
                'cost' => $c->cost,
                'amount' => $c->amount,
                'currency' => $c->currency,
                'ex_rate' => $c->ex_rate,
                'amount_in_dollar' => $c->amount_in_dollar,
                'freight_type' => $c->freight_type,
                'pa_party_tpa_agent' => $c->pa_party_tpa_agent,
                'slot_term' => $c->slotTerm?->term,
                'hide' => $c->hide,
                'remarks' => $c->remarks,
            ];
        })->values()->all();

        $costConfig = [
            'bookingId' => $booking->id,
            'charges' => $charges->map(fn ($c) => ['id' => $c->id, 'label' => $c->name])->values()->all(),
            'sizes' => $containerSizes->map(fn ($s) => ['id' => $s->id, 'label' => $s->size])->values()->all(),
            'types' => $containerTypes->map(fn ($t) => ['id' => $t->id, 'label' => $t->name])->values()->all(),
            'currencies' => $currencies,
            'rates' => $rates,
            'parties' => $parties->pluck('name')->all(),
            'freightTypes' => $revenueFreightTypes,
            'slotTerms' => $slotTerms,
            'rows' => $costRows,
        ];
    @endphp
    <script type="application/json" id="cost-grid-config">@json($costConfig)</script>
</div>
