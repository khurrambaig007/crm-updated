<x-app-layout :title="$tabTitle">
    @php
        $submitClasses = 'rounded-lg bg-primary-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-500 transition';
        $ghostClasses = 'rounded-lg bg-white px-4 py-2 text-sm font-semibold text-primary-900 ring-1 ring-inset ring-card-border transition-colors hover:bg-primary-50';
    @endphp

    <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
        @include('bl-info._header', ['headerSubtitle' => $tabDescription])
        @include('bl-info._nav')

        <div class="rounded-2xl bg-card-bg p-6 shadow-sm ring-1 ring-card-border transition-shadow hover:shadow-md sm:p-8">
            <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-primary-600">
                        @include('components.icons.box', ['classes' => 'h-4 w-4'])
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Container Info</h2>
                        <p class="text-xs text-topbar-muted">
                            One row per equipment line. Size, weights and units carry over from the booking;
                            everything else is yours to fill in.
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="hidden text-xs text-topbar-muted sm:inline">
                        Double-click a cell to edit &middot; Save writes that row
                    </span>
                    <button type="button" id="container-info-add-row" class="{{ $submitClasses }} inline-flex items-center gap-2">
                        @include('components.icons.box', ['classes' => 'h-4 w-4'])
                        Add Container
                    </button>
                </div>
            </div>

            {{-- Legend for the row-state colours used by the grid's getRowStyle --}}
            <div class="mb-4 flex flex-wrap items-center gap-4 text-xs text-topbar-muted">
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-3 w-3 rounded-sm bg-red-50 ring-1 ring-red-200"></span>
                    New &mdash; not saved yet
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <span class="h-3 w-3 rounded-sm bg-yellow-50 ring-1 ring-yellow-200"></span>
                    Unsaved edits
                </span>
            </div>

            <div id="container-info-grid" class="ag-theme-quartz overflow-hidden rounded-xl ring-1 ring-card-border shadow-sm" style="width: 100%; height: 460px;"></div>

            <div class="mt-6 flex flex-wrap items-center justify-end gap-3 pt-2">
                <a href="{{ route('bookings.edit', $booking) }}" class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted transition-colors hover:text-topbar-text">
                    Back to Booking
                </a>
            </div>
        </div>
    </div>

    @php
        $slotMap = collect($slotTerms)->pluck('label', 'id');

        $rows = $booking->blContainerInfos()
            ->orderByRaw('equipment_id IS NULL')
            ->orderBy('id')
            ->get()
            ->map(fn ($row) => [
                'id' => $row->id,
                'cntr_owner' => $row->container_info_cntr_owner,
                'size' => $row->container_info_size,
                'container_no' => $row->container_info_container_no,
                'seal_no' => $row->container_info_seal_no,
                'seal2_no' => $row->container_info_seal2_no,
                'load_type' => $row->container_info_load_type,
                'gross_weight' => $row->container_info_gross_weight,
                'net_weight' => $row->container_info_net_weight,
                'cbm' => $row->container_info_cbm,
                'packages' => $row->container_info_packages,
                'unit' => $row->container_info_unit,
                'temperature' => $row->container_info_temperature,
                'slot_operator' => $row->container_info_slot_operator,
                'slot_operator_ts1' => $row->container_info_slot_operator_ts1,
                'slot_operator_ts2' => $row->container_info_slot_operator_ts2,
                'slot_operator_label' => $slotMap->get($row->container_info_slot_operator),
                'slot_operator_ts1_label' => $slotMap->get($row->container_info_slot_operator_ts1),
                'slot_operator_ts2_label' => $slotMap->get($row->container_info_slot_operator_ts2),
                'imo_class' => $row->container_info_imo_class,
                'un_number' => $row->container_info_un_number,
                'remarks' => $row->container_info_remarks,
            ])
            ->values()
            ->all();

        $containerInfoConfig = [
            'bookingId' => $booking->id,
            'canEdit' => auth()->user()->isSuperAdmin() || auth()->user()->can('bl_info.edit'),
            'cntrOwners' => collect($cntrOwners)->map(fn ($label, $id) => ['id' => $id, 'label' => $label])->values()->all(),
            'slots' => $slotTerms,
            'rows' => $rows,
        ];
    @endphp
    <script type="application/json" id="container-info-grid-config">@json($containerInfoConfig)</script>

    @push('scripts')
        <style>
            #container-info-grid.ag-theme-quartz {
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
                --ag-header-column-moving-background-color: var(--color-primary-800);
                --ag-selected-row-background-color: var(--color-primary-100);
            }
        </style>
    @endpush
</x-app-layout>