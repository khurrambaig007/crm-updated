import { createGrid, ModuleRegistry, AllCommunityModule } from 'ag-grid-community';
import $ from 'jquery';

ModuleRegistry.registerModules([AllCommunityModule]);

/**
 * BL Info > Container Info.
 *
 * Rows arrive pre-created from the booking's equipment lines, so the grid is
 * mostly an editing surface: inline edits are buffered on the row and only
 * written when that row's Save button is pressed. Saves are per-row rather
 * than a bulk submit because every row owns a separate DB record.
 */

let gridApi = null;
let config = null;
// Both maps run id -> label for display and label -> id for writes.
let labelByCntrOwner = {};
let cntrOwnerByLabel = {};
let labelBySlot = {};
let slotByLabel = {};

function canEdit() {
    return !config || config.canEdit !== false;
}

function actionsRenderer(params) {
    const container = document.createElement('div');
    container.className = 'flex items-center gap-1 h-full';

    if (!canEdit()) {
        return container;
    }

    const $saveBtn = $('<button>', {
        type: 'button',
        class: 'inline-flex items-center justify-center rounded p-1 text-emerald-600 hover:bg-emerald-50',
        title: 'Save',
        html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8 15 8"/></svg>',
    });
    const $delBtn = $('<button>', {
        type: 'button',
        class: 'inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50',
        title: 'Delete',
        html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
    });

    $saveBtn.on('click', (e) => {
        e.stopPropagation();
        window['__bcig_save'](params);
    });
    $delBtn.on('click', (e) => {
        e.stopPropagation();
        window['__bcig_delete'](params);
    });

    $(container).append($saveBtn).append($delBtn);

    return container;
}

function csrfToken() {
    return $('meta[name="csrf-token"]').attr('content') || '';
}

function emptyToNull(value) {
    if (value === '' || value === undefined) {
        return null;
    }

    return String(value).trim() === '' ? null : value;
}

/**
 * Blank stays blank; a real value becomes the id the server validates on.
 *
 * Number(null) is 0, which would be rejected by the in:/exists rules, so null
 * needs its own branch — the same class of bug numOrNull guards in the other
 * grids.
 */
function idOrNull(value) {
    if (value === '' || value === undefined || value === null) {
        return null;
    }

    const num = Number(value);

    return isNaN(num) ? null : num;
}

/**
 * Tolerant display for the numeric columns.
 *
 * agGrid pins cellDataType from rowData[0] and then applies that type's strict
 * formatter to every cell, which renders "Invalid Number" for numeric strings.
 * An explicit parseFloat-based formatter overrides the inferred one, so these
 * must stay declared even though the values are stored as text.
 */
function decimalFormatter(params) {
    const value = params.value;

    if (value === null || value === undefined || value === '') {
        return '';
    }

    const num = parseFloat(value);

    if (isNaN(num)) {
        return String(value);
    }

    return num.toLocaleString('en-US', { maximumFractionDigits: 3 });
}

function buildPayload(data) {
    return {
        container_info_cntr_owner: idOrNull(data.cntr_owner),
        container_info_size: emptyToNull(data.size),
        container_info_container_no: emptyToNull(data.container_no),
        container_info_seal_no: emptyToNull(data.seal_no),
        container_info_seal2_no: emptyToNull(data.seal2_no),
        container_info_load_type: emptyToNull(data.load_type),
        container_info_gross_weight: emptyToNull(data.gross_weight),
        container_info_net_weight: emptyToNull(data.net_weight),
        container_info_cbm: emptyToNull(data.cbm),
        container_info_packages: emptyToNull(data.packages),
        container_info_unit: emptyToNull(data.unit),
        container_info_temperature: emptyToNull(data.temperature),
        container_info_slot_operator: idOrNull(data.slot_operator),
        container_info_slot_operator_ts1: idOrNull(data.slot_operator_ts1),
        container_info_slot_operator_ts2: idOrNull(data.slot_operator_ts2),
        container_info_imo_class: emptyToNull(data.imo_class),
        container_info_un_number: emptyToNull(data.un_number),
        container_info_remarks: emptyToNull(data.remarks),
    };
}

function onCellValueChanged(event) {
    if (event.node.data) {
        event.node.data.__dirty = true;
    }
}

window['__bcig_save'] = function (params) {
    if (!gridApi || !config || !canEdit()) {
        return;
    }

    gridApi.stopEditing();

    const data = params.node.data;
    const base = '/bookings/' + config.bookingId + '/bl-info/container-info';
    const url = data.id ? base + '/' + data.id : base;

    $.ajax({
        url: url,
        method: data.id ? 'PATCH' : 'POST',
        data: buildPayload(data),
        headers: { 'X-CSRF-TOKEN': csrfToken() },
        success: function (response) {
            const saved = response.row;

            const updated = Object.assign({}, data, saved, { __dirty: false });
            params.node.setData(updated);
            gridApi.redrawRows({ rowNodes: [params.node] });
            window.Alerts.toast(response.message, 'success');
        },
        error: function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                window.Alerts.validation(Object.values(xhr.responseJSON.errors).flat());
            } else {
                window.Alerts.error('Failed to save container.');
            }
        },
    });
};

window['__bcig_delete'] = function (params) {
    if (!gridApi || !config || !canEdit()) {
        return;
    }

    const data = params.node.data;

    window.Alerts.confirm({
        title: 'Delete Container?',
        text: 'This container entry will be removed permanently.',
        confirmText: 'Yes, delete it',
    }).then(function (confirmed) {
        if (!confirmed) {
            return;
        }

        if (!data.id) {
            gridApi.applyTransaction({ remove: [data] });
            window.Alerts.toast('Row removed.', 'success');
            return;
        }

        $.ajax({
            url: '/bookings/' + config.bookingId + '/bl-info/container-info/' + data.id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken() },
            success: function (response) {
                gridApi.applyTransaction({ remove: [data] });
                window.Alerts.toast(response.message, 'success');
            },
            error: function () {
                window.Alerts.error('Failed to delete container.');
            },
        });
    });
};

window.BCIG = {
    addRow: function () {
        if (!gridApi || !config || !canEdit()) {
            return;
        }

        const newRow = {
            id: null,
            cntr_owner: null,
            size: '',
            container_no: '',
            seal_no: '',
            seal2_no: '',
            load_type: '',
            gross_weight: '',
            net_weight: '',
            cbm: '',
            packages: '',
            unit: '',
            temperature: '',
            slot_operator: null,
            slot_operator_ts1: null,
            slot_operator_ts2: null,
            imo_class: '',
            un_number: '',
            remarks: '',
        };

        gridApi.applyTransaction({ add: [newRow], addIndex: 0 });
    },
};

$(function () {
    const $mount = $('#container-info-grid');
    const $configTag = $('#container-info-grid-config');

    if ($mount.length === 0 || $configTag.length === 0) {
        return;
    }

    config = JSON.parse($configTag.text() || '{}');

    labelByCntrOwner = {};
    cntrOwnerByLabel = {};
    labelBySlot = {};
    slotByLabel = {};

    $.each(config.cntrOwners || [], function (i, item) {
        cntrOwnerByLabel[item.label] = item.id;
        labelByCntrOwner[item.id] = item.label;
    });

    $.each(config.slots || [], function (i, item) {
        slotByLabel[item.label] = item.id;
        labelBySlot[item.id] = item.label;
    });

    const cntrOwnerLabels = (config.cntrOwners || []).map(function (item) {
        return item.label;
    });

    const slotLabels = (config.slots || []).map(function (item) {
        return item.label;
    });

    const editable = canEdit();

    /**
     * A dropdown cell that shows a label but keeps the id on the row.
     *
     * valueGetter is what agGrid actually renders, so a valueFormatter is not
     * enough here: without it the cell would display the raw slot_terms /
     * cntr_owner id. The valueSetter writes the picked id back onto the row,
     * and that id is what buildPayload posts.
     */
    const lookupColumn = function (options) {
        return {
            field: options.field,
            headerName: options.headerName,
            minWidth: options.minWidth,
            editable: editable,
            cellEditor: 'agSelectCellEditor',
            cellEditorParams: { values: options.labels },
            valueGetter: function (params) {
                var id = params.data ? params.data[options.field] : null;
                return id === null || id === undefined ? '' : options.byId[id];
            },
            valueSetter: function (params) {
                params.data[options.field] = params.newValue === '' ? null : options.byLabel[params.newValue];
                return true;
            },
        };
    };

    const columnDefs = [
        {
            field: 'actions',
            headerName: 'Actions',
            width: 120,
            sortable: false,
            filter: false,
            editable: false,
            pinned: 'left',
            cellRenderer: actionsRenderer,
        },
        lookupColumn({
            field: 'cntr_owner',
            headerName: 'Cntr Owner',
            minWidth: 130,
            labels: cntrOwnerLabels,
            byId: labelByCntrOwner,
            byLabel: cntrOwnerByLabel,
        }),
        { field: 'size', headerName: 'Size/Type', minWidth: 170, editable: editable },
        { field: 'container_no', headerName: 'Container#', minWidth: 140, editable: editable },
        { field: 'seal_no', headerName: 'Seal#', minWidth: 120, editable: editable },
        { field: 'seal2_no', headerName: 'Seal2 #', minWidth: 120, editable: editable },
        { field: 'load_type', headerName: 'Load Type', minWidth: 120, editable: editable },
        { field: 'gross_weight', headerName: 'Gross WT', minWidth: 110, editable: editable, valueFormatter: decimalFormatter },
        { field: 'net_weight', headerName: 'Net Wt', minWidth: 110, editable: editable, valueFormatter: decimalFormatter },
        { field: 'cbm', headerName: 'CBM', minWidth: 100, editable: editable, valueFormatter: decimalFormatter },
        { field: 'packages', headerName: 'Packages', minWidth: 110, editable: editable, valueFormatter: decimalFormatter },
        { field: 'unit', headerName: 'Unit', minWidth: 90, editable: editable },
        { field: 'temperature', headerName: 'Temperature', minWidth: 120, editable: editable },
        lookupColumn({
            field: 'slot_operator',
            headerName: 'Slot Operator',
            minWidth: 150,
            labels: slotLabels,
            byId: labelBySlot,
            byLabel: slotByLabel,
        }),
        lookupColumn({
            field: 'slot_operator_ts1',
            headerName: 'Slot Operator (TS1)',
            minWidth: 160,
            labels: slotLabels,
            byId: labelBySlot,
            byLabel: slotByLabel,
        }),
        lookupColumn({
            field: 'slot_operator_ts2',
            headerName: 'Slot Operator (TS2)',
            minWidth: 160,
            labels: slotLabels,
            byId: labelBySlot,
            byLabel: slotByLabel,
        }),
        { field: 'imo_class', headerName: 'IMO Class', minWidth: 110, editable: editable },
        { field: 'un_number', headerName: 'UN#', minWidth: 110, editable: editable },
        { field: 'remarks', headerName: 'Remarks', minWidth: 180, editable: editable, flex: 1 },
    ];

    gridApi = createGrid($mount[0], {
        theme: 'legacy',
        columnDefs: columnDefs,
        rowData: config.rows || [],
        defaultColDef: {
            editable: editable,
            resizable: true,
        },
        rowHeight: 44,
        suppressCellFocus: false,
        stopEditingWhenCellsLoseFocus: true,
        onCellValueChanged: onCellValueChanged,
        getRowStyle: function (params) {
            if (!params.data.id) {
                return { backgroundColor: '#fef2f2' };
            }
            if (params.data.__dirty) {
                return { backgroundColor: '#fefce8' };
            }
            return null;
        },
    });

    // Revenue and cost publish their api for cross-module reads (totals); the
    // equipment grid is queried only through these globals in tests too.
    window['__bcig_api'] = gridApi;

    $('#container-info-add-row').on('click', function () {
        window.BCIG.addRow();
    });
});