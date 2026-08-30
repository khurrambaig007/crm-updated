import { createGrid, ModuleRegistry, AllCommunityModule } from 'ag-grid-community';
import $ from 'jquery';

ModuleRegistry.registerModules([AllCommunityModule]);

let gridApi = null;
let config = null;
let chargeMap = {};
let sizeMap = {};
let typeMap = {};
let slotTermMap = {};

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
        window['__bcg_save'](params);
    });
    $delBtn.on('click', function (e) {
        e.stopPropagation();
        window['__bcg_delete'](params);
    });

    $(container).append($saveBtn).append($delBtn);

    return container;
}

function csrfToken() {
    return $('meta[name="csrf-token"]').attr('content') || '';
}

function numOrNull(value) {
    if (value === '' || value === undefined || value === null) {
        return null;
    }
    var parsed = parseFloat(value);
    return isNaN(parsed) ? null : parsed;
}

function buildPayload(data) {
    return {
        charge_id: chargeMap[data.charges],
        container_size_id: sizeMap[data.size],
        container_type_id: typeMap[data.type],
        quantity: numOrNull(data.quantity),
        mrg: numOrNull(data.mrg),
        cost: numOrNull(data.cost),
        amount: numOrNull(data.amount),
        currency: data.currency || null,
        ex_rate: numOrNull(data.ex_rate),
        amount_in_dollar: numOrNull(data.amount_in_dollar),
        freight_type: data.freight_type || null,
        pa_party_tpa_agent: data.pa_party_tpa_agent || null,
        slot_term: data.slot_term ? slotTermMap[data.slot_term] : null,
        hide: data.hide === 'Yes' || data.hide === true ? 1 : 0,
        remarks: data.remarks || null,
    };
}

function applySaved(saved, data) {
    return Object.assign({}, data, {
        id: saved.id,
        charges: saved.charges,
        size: saved.size,
        type: saved.type,
        quantity: saved.quantity,
        mrg: saved.mrg,
        cost: saved.cost,
        amount: saved.amount,
        currency: saved.currency,
        ex_rate: saved.ex_rate,
        amount_in_dollar: saved.amount_in_dollar,
        freight_type: saved.freight_type,
        pa_party_tpa_agent: saved.pa_party_tpa_agent,
        slot_term: saved.slot_term,
        hide: saved.hide ? 'Yes' : 'No',
        remarks: saved.remarks,
    });
}

function recalcRow(node) {
    var data = node.data;
    var quantity = parseFloat(data.quantity) || 0;
    var mrg = parseFloat(data.mrg) || 0;
    var cost = parseFloat(data.cost) || 0;
    var amount = +(quantity * mrg * cost).toFixed(2);
    data.amount = amount;

    var exRate = parseFloat(data.ex_rate) || 0;
    data.amount_in_dollar = exRate ? +(amount / exRate).toFixed(2) : 0;

    node.setData(data);
}

function numberFormatter(params) {
    if (params.value === null || params.value === undefined || params.value === '') {
        return '';
    }
    var num = parseFloat(params.value);
    if (isNaN(num)) {
        return params.value;
    }
    return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function onCellValueChanged(event) {
    if (event.node.data) {
        event.node.data.__dirty = true;
    }
    if (event.colDef.field === 'currency') {
        var code = event.newValue;
        if (code && config && config.rates && config.rates[code]) {
            event.node.setDataValue('ex_rate', config.rates[code]);
            return;
        }
    }
    recalcRow(event.node);
}

window['__bcg_save'] = function (params) {
    if (!gridApi || !config) {
        return;
    }

    gridApi.stopEditing();
    var data = params.node.data;
    var url = data.id
        ? '/bookings/' + config.bookingId + '/costs/' + data.id
        : '/bookings/' + config.bookingId + '/costs';

    if (!data.charges || !data.size || !data.type) {
        window.Alerts.error('Charges, Size and Type are required.');
        return;
    }

    $.ajax({
        url: url,
        method: data.id ? 'PATCH' : 'POST',
        data: buildPayload(data),
        headers: { 'X-CSRF-TOKEN': csrfToken() },
        success: function (response) {
            var savedData = applySaved(response.row, data);
            savedData.__dirty = false;
            params.node.setData(savedData);
            gridApi.redrawRows({ rowNodes: [params.node] });
            if (window.computeBookingTotals) {
                window.computeBookingTotals();
            }
            window.Alerts.toast(response.message, 'success');
        },
        error: function (xhr) {
            if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                window.Alerts.validation(Object.values(xhr.responseJSON.errors).flat());
            } else {
                window.Alerts.error('Failed to save cost.');
            }
        },
    });
};

window['__bcg_delete'] = function (params) {
    if (!gridApi || !config) {
        return;
    }

    var data = params.node.data;

    window.Alerts.confirm({
        title: 'Delete Cost?',
        text: 'This cost entry will be removed permanently.',
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
            url: '/bookings/' + config.bookingId + '/costs/' + data.id,
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken() },
            success: function (response) {
                gridApi.applyTransaction({ remove: [data] });
                if (window.computeBookingTotals) {
                    window.computeBookingTotals();
                }
                window.Alerts.toast(response.message, 'success');
            },
            error: function () {
                window.Alerts.error('Failed to delete cost.');
            },
        });
    });
};

window.BCG = {
    addRow: function () {
        if (!gridApi || !config) {
            return;
        }

        var $charges = $('#cs-charges');
        var $size = $('#cs-size');
        var $type = $('#cs-type');

        if (!$charges.val() || !$size.val() || !$type.val()) {
            window.Alerts.error('Please select Charges, Size and Type first.');
            return;
        }

        var newRow = {
            id: null,
            charges: $charges.find('option:selected').text(),
            size: $size.find('option:selected').text(),
            type: $type.find('option:selected').text(),
            quantity: $('#cs-quantity').val() !== '' ? parseFloat($('#cs-quantity').val()) : null,
            mrg: $('#cs-mrg').val() !== '' ? parseFloat($('#cs-mrg').val()) : null,
            cost: $('#cs-cost').val() !== '' ? parseFloat($('#cs-cost').val()) : null,
            amount: '',
            currency: $('#cs-currency').val() || null,
            ex_rate: $('#cs-ex-rate').val() !== '' ? parseFloat($('#cs-ex-rate').val()) : null,
            amount_in_dollar: '',
            freight_type: '',
            pa_party_tpa_agent: '',
            slot_term: '',
            hide: 'No',
            remarks: '',
        };

        var res = gridApi.applyTransaction({ add: [newRow], addIndex: 0 });
        if (res && res.add && res.add.length) {
            recalcRow(res.add[0]);
        }

        this.clearForm();
    },
    clearForm: function () {
        $('#cs-charges').val('');
        $('#cs-size').val('');
        $('#cs-type').val('');
        $('#cs-quantity').val('');
        $('#cs-mrg').val('');
        $('#cs-cost').val('');
        $('#cs-currency').val('');
        $('#cs-ex-rate').val('1');
    },
};

$(function () {
    var $mount = $('#costs-grid');
    var $configTag = $('#cost-grid-config');
    if ($mount.length === 0 || $configTag.length === 0) {
        return;
    }

    $('#cs-add-row').on('click', function () {
        window.BCG.addRow();
    });
    $('#cs-clear-form').on('click', function () {
        window.BCG.clearForm();
    });
    $('#cs-currency').on('change', function () {
        var rate = (config && config.rates && config.rates[this.value]) || 1;
        $('#cs-ex-rate').val(rate);
    });

    config = JSON.parse($configTag.text() || '{}');

    chargeMap = {};
    sizeMap = {};
    typeMap = {};
    slotTermMap = {};
    $.each(config.charges || [], function (i, item) {
        chargeMap[item.label] = item.id;
    });
    $.each(config.sizes || [], function (i, item) {
        sizeMap[item.label] = item.id;
    });
    $.each(config.types || [], function (i, item) {
        typeMap[item.label] = item.id;
    });
    $.each(config.slotTerms || [], function (i, item) {
        slotTermMap[item.label] = item.id;
    });

    var chargeLabels = (config.charges || []).map(function (item) {
        return item.label;
    });
    var sizeLabels = (config.sizes || []).map(function (item) {
        return item.label;
    });
    var typeLabels = (config.types || []).map(function (item) {
        return item.label;
    });
    var currencyCodes = Object.keys(config.currencies || {});
    var partyLabels = config.parties || [];
    var freightTypeLabels = config.freightTypes || [];
    var slotTermLabels = (config.slotTerms || []).map(function (item) {
        return item.label;
    });

    var rows = (config.rows || []).map(function (row) {
        return Object.assign({}, row, {
            hide: row.hide ? 'Yes' : 'No',
        });
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
            field: 'id',
            headerName: 'Id',
            width: 80,
            editable: false,
        },
        {
            field: 'charges',
            headerName: 'Charges',
            flex: 1,
            minWidth: 140,
            cellEditor: 'agSelectCellEditor',
            cellEditorParams: { values: chargeLabels },
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
            field: 'slot_term',
            headerName: 'Slot Term',
            flex: 1,
            minWidth: 130,
            cellEditor: 'agSelectCellEditor',
            cellEditorParams: { values: slotTermLabels },
        },
        {
            field: 'quantity',
            headerName: 'Quantity',
            flex: 1,
            minWidth: 100,
            valueFormatter: numberFormatter,
        },
        {
            field: 'mrg',
            headerName: 'Mrg',
            flex: 1,
            minWidth: 90,
            valueFormatter: numberFormatter,
        },
        {
            field: 'cost',
            headerName: 'Cost',
            flex: 1,
            minWidth: 90,
            valueFormatter: numberFormatter,
        },
        {
            field: 'amount',
            headerName: 'Amount',
            flex: 1,
            minWidth: 100,
            valueFormatter: numberFormatter,
        },
        {
            field: 'currency',
            headerName: 'Currency',
            flex: 1,
            minWidth: 110,
            cellEditor: 'agSelectCellEditor',
            cellEditorParams: { values: currencyCodes },
        },
        {
            field: 'ex_rate',
            headerName: 'Ex. Rate',
            flex: 1,
            minWidth: 100,
            valueFormatter: numberFormatter,
        },
        {
            field: 'amount_in_dollar',
            headerName: 'Amount In Dollar',
            flex: 1,
            minWidth: 150,
            valueFormatter: numberFormatter,
        },
        {
            field: 'freight_type',
            headerName: 'Freight Type',
            flex: 1,
            minWidth: 130,
            cellEditor: 'agSelectCellEditor',
            cellEditorParams: { values: freightTypeLabels },
        },
        {
            field: 'pa_party_tpa_agent',
            headerName: 'Pa Party / TPA Agent',
            flex: 1,
            minWidth: 170,
            cellEditor: 'agSelectCellEditor',
            cellEditorParams: { values: partyLabels },
        },
        {
            field: 'hide',
            headerName: 'Hide',
            width: 100,
            cellEditor: 'agSelectCellEditor',
            cellEditorParams: { values: ['Yes', 'No'] },
        },
        {
            field: 'remarks',
            headerName: 'Remarks',
            flex: 1,
            minWidth: 150,
        },
    ];

    gridApi = createGrid($mount[0], {
        theme: 'legacy',
        columnDefs: columnDefs,
        rowData: rows,
        defaultColDef: {
            editable: true,
            resizable: true,
        },
        rowHeight: 44,
        stopEditingWhenCellsLoseFocus: true,
        onCellValueChanged: onCellValueChanged,
        onRowDataUpdated: function () {
            if (window.computeBookingTotals) {
                window.computeBookingTotals();
            }
        },
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

    window.__costGridApi = gridApi;
    if (window.computeBookingTotals) {
        window.computeBookingTotals();
    }
});
