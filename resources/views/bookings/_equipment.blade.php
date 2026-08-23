<style>
    #equipments-grid.ag-theme-quartz {
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
    {{-- Row 1: Equipment input row --}}
    <div class="mb-5 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div>
            {!! html()->label('Size', 'eq-size')->class($labelClasses) !!}
            <div class="relative">
                <select name="eq_size" id="eq-size" class="{{ $selectClasses }}">
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
            {!! html()->label('Type', 'eq-type')->class($labelClasses) !!}
            <div class="relative">
                <select name="eq_type" id="eq-type" class="{{ $selectClasses }}">
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
            {!! html()->label('Quantity', 'eq-quantity')->class($labelClasses) !!}
            {!! html()->number('eq_quantity', old('eq_quantity'))->id('eq-quantity')->class($inputClasses)->attribute('step', 'any')->attribute('min', '0') !!}
        </div>
        <div>
            {!! html()->label('Approval Status', 'eq-approval-status')->class($labelClasses) !!}
            <div class="relative">
                <select name="eq_approval_status" id="eq-approval-status" class="{{ $selectClasses }}">
                    <option value="">Select Status</option>
                    @foreach (config('dropdowns.bookings.approval_status') as $statusValue => $statusLabel)
                        <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m6 9 6 6 6-6"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Row 2: agGrid --}}
    <div class="flex items-center justify-end">
        <button type="button" id="eq-add-row" class="{{ $submitClasses }} inline-flex items-center gap-2">
            Add Equipment
        </button>
    </div>

    <div id="equipments-grid" class="ag-theme-quartz overflow-hidden rounded-xl ring-1 ring-card-border shadow-sm" style="width: 100%; height: 320px;"></div>

    @php
        $equipmentRows = $booking->equipments->map(function ($e) {
            return [
                'id' => $e->id,
                'size_id' => $e->size,
                'type_id' => $e->type,
                'quantity' => $e->quantity,
                'approval_status' => $e->approval_status,
                'gross_weight' => $e->gross_weight,
                'packages' => $e->packages,
                'unit' => $e->unit,
                'cargo_volumn' => $e->cargo_volumn,
                'size' => $e->containerSize?->size,
                'type' => $e->containerType?->name,
            ];
        })->values()->all();

        $equipmentConfig = [
            'bookingId' => $booking->id,
            'sizes' => $containerSizes->map(fn ($s) => ['id' => $s->id, 'label' => $s->size])->values()->all(),
            'types' => $containerTypes->map(fn ($t) => ['id' => $t->id, 'label' => $t->name])->values()->all(),
            'rows' => $equipmentRows,
        ];
    @endphp
    <script type="application/json" id="equipment-grid-config">@json($equipmentConfig)</script>
</div>
