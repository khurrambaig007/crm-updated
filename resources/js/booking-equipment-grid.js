import { createGrid, ModuleRegistry, AllCommunityModule } from 'ag-grid-community';
import $ from 'jquery';

ModuleRegistry.registerModules([AllCommunityModule]);

let gridApi = null;
let config = null;
let sizeMap = {};
let typeMap = {};

function actionsRenderer(params) {
    var container = document.createElement('div');
    container.className = 'flex items-center gap-1 h-full';

    var $saveBtn = $('<button>', {
        type: 'button',
        class: 'inline-flex items-center justify-center rounded p-1 text-emerald-600 hover:bg-emerald-50',
        title: 'Save',
        html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8 15 8"/></svg>',
    });
    var $delBtn = $('<button>', {
        type: 'button',
        class: 'inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50',
        title: 'Delete',
        html: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>',
    });

    $saveBtn.on('click', function (e) {
        e.stopPropagation();
        window['__beg_save'](params);
    });
    $delBtn.on('click', function (e) {
        e.stopPropagation();
        window['__beg_delete'](params);
    });

    $(container).append($saveBtn).append($delBtn);

    return container;
}

function csrfToken() {
    return $('meta[name="csrf-token"]').attr('content') || '';
}

function buildPayload(data) {
    return {
        size: sizeMap[data.size],
        type: typeMap[data.type],
        quantity: data.quantity === '' || data.quantity === undefined || data.quantity === null ? null : parseFloat(data.quantity),
        approval_status: parseInt(data.approval_status, 10) || 1,
        gross_weight: data.gross_weight || null,
        packages: data.packages || null,
        unit: data.unit === '' || data.unit === undefined || data.unit === null ? null : parseInt(data.unit, 10),
        cargo_volumn: data.cargo_volumn || null,
    };
}

window['__beg_save'] = function (params) {
    if (!gridApi || !config) {
        return;
    }

    gridApi.stopEditing();
    var data = params.node.data;
    var url = data.id
        ? '/bookings/' + config.bookingId + '/equipments/' + data.id
        : '/bookings/' + config.bookingId + '/equipments';

    if (!data.size || !data.type) {
        window.Alerts.error('Size and Type are required.');
        return;
    }

    $.ajax({
        url: url,
        method: data.id ? 'PATCH' : 'POST',
        data: buildPayload(data),
        headers: { 'X-CSRF-TOKEN': csrfToken() },
        success: function (response) {
            var saved = response.row;
            var updated = Object.assign({}, data, {
                id: saved.id,
                size_id: saved.size_id,
                type_id: saved.type_id,
                quantity: saved.quantity,
                approval_status: saved.approval_status,
                gross_weight: saved.gross_weight,
                packages: saved.packages,
                unit: saved.unit,
                cargo_volumn: saved.cargo_volumn,
                size: saved.size,
                type: saved.type,
            });
            params.node.setData(updated);
            window.Alerts.toast(response.message, 'success');
        },
        error: function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                window.Alerts.validation(Object.values(xhr.responseJSON.errors).flat());
            } else {
                window.Alerts.error('Failed to save equipment.');
            }
        },
    });
};

window['__beg_delete'] = function (params) {
    if (!gridApi || !config) {
        return;
    }

    var data = params.node.data;

    window.Alerts.confirm({
        title: 'Delete Equipment?',
        text: 'This equipment entry will be removed permanently.',
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
            url: '/bookings/' + config.bookingId + '/equipments/' + data.id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken() },
            success: function (response) {
                gridApi.applyTransaction({ remove: [data] });
                window.Alerts.toast(response.message, 'success');
            },
            error: function () {
                window.Alerts.error('Failed to delete equipment.');
            },
        });
    });
};

window.BEG = {
    addRow: function () {
        if (!gridApi || !config) {
            return;
        }

        var $size = $('#eq-size');
        var $type = $('#eq-type');

        if (!$size.val() || !$type.val()) {
            window.Alerts.error('Please select Size and Type first.');
            return;
        }

        var newRow = {
            id: null,
            size: $size.find('option:selected').text(),
            type: $type.find('option:selected').text(),
            size_id: parseInt($size.val(), 10),
            type_id: parseInt($type.val(), 10),
            quantity: $('#eq-quantity').val() !== '' ? parseFloat($('#eq-quantity').val()) : null,
            approval_status: parseInt($('#eq-approval-status').val(), 10) || 1,
            gross_weight: '',
            packages: '',
            unit: '',
            cargo_volumn: '',
        };

        gridApi.applyTransaction({ add: [newRow], addIndex: 0 });
    },
};

$(function () {
    var $mount = $('#equipments-grid');
    var $configTag = $('#equipment-grid-config');
    if ($mount.length === 0 || $configTag.length === 0) {
        return;
    }

    $('#eq-add-row').on('click', function () {
        window.BEG.addRow();
    });

    config = JSON.parse($configTag.text() || '{}');

    sizeMap = {};
    typeMap = {};
    $.each(config.sizes || [], function (i, item) {
        sizeMap[item.label] = item.id;
    });
    $.each(config.types || [], function (i, item) {
        typeMap[item.label] = item.id;
    });

    var sizeLabels = (config.sizes || []).map(function (item) {
        return item.label;
    });
    var typeLabels = (config.types || []).map(function (item) {
        return item.label;
    });

    var columnDefs = [
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
        {
            field: 'size',
            headerName: 'Size',
            flex: 1,
            minWidth: 110,
            cellEditor: 'agSelectCellEditor',
            cellEditorParams: { values: sizeLabels },
        },
        {
            field: 'type',
            headerName: 'Type',
            flex: 1,
            minWidth: 130,
            cellEditor: 'agSelectCellEditor',
            cellEditorParams: { values: typeLabels },
        },
        {
            field: 'quantity',
            headerName: 'Quantity',
            flex: 1,
            minWidth: 100,
        },
        {
            field: 'gross_weight',
            headerName: 'Gross Weight',
            flex: 1,
            minWidth: 130,
        },
        {
            field: 'packages',
            headerName: 'Packages',
            flex: 1,
            minWidth: 110,
        },
        {
            field: 'unit',
            headerName: 'Unit',
            flex: 1,
            minWidth: 90,
        },
        {
            field: 'cargo_volumn',
            headerName: 'Cargo Volume',
            flex: 1,
            minWidth: 130,
        },
    ];

    gridApi = createGrid($mount[0], {
        theme: 'legacy',
        columnDefs: columnDefs,
        rowData: config.rows || [],
        defaultColDef: {
            editable: true,
            resizable: true,
        },
        rowHeight: 44,
        suppressCellFocus: false,
        stopEditingWhenCellsLoseFocus: true,
        getRowStyle: function (params) {
            if (!params.data.id) {
                return { backgroundColor: '#fef2f2' };
            }
            return null;
        },
    });
});
