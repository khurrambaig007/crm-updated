import { createGrid, ModuleRegistry, AllCommunityModule } from 'ag-grid-community';

ModuleRegistry.registerModules([AllCommunityModule]);

let gridApi = null;
let config = null;
let rowCounter = 0;

function ActionsRenderer(params) {
    this.params = params;
    this.eGui = document.createElement('div');
    this.eGui.className = 'flex items-center gap-1';

    this.saveBtn = document.createElement('button');
    this.saveBtn.type = 'button';
    this.saveBtn.className = 'inline-flex items-center justify-center rounded p-1 text-emerald-600 hover:bg-emerald-50';
    this.saveBtn.title = 'Save';
    this.saveBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>';

    this.delBtn = document.createElement('button');
    this.delBtn.type = 'button';
    this.delBtn.className = 'inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50';
    this.delBtn.title = 'Delete';
    this.delBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>';

    this.saveBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        window.PIG.saveRow(this.params);
    });
    this.delBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        window.PIG.deleteRow(this.params);
    });

    this.eGui.appendChild(this.saveBtn);
    this.eGui.appendChild(this.delBtn);
}

ActionsRenderer.prototype.getGui = function () {
    return this.eGui;
};

ActionsRenderer.prototype.refresh = function () {
    return true;
};

function recalcRow(node) {
    const data = node.data;
    const amount = parseFloat(data.amount) || 0;
    const rate = parseFloat(data.exchange_rate) || 0;
    const vatPct = parseFloat(data.vat_percentage) || 0;

    data.amount_dollar = +(amount * rate).toFixed(2);
    data.vat_amount = +(amount * (vatPct / 100)).toFixed(2);
    data.vat_amount_dollar = +(data.vat_amount * rate).toFixed(2);

    node.setData(data);
}

function onCellValueChanged(event) {
    if (['amount', 'exchange_rate', 'vat_percentage'].includes(event.colDef.field)) {
        recalcRow(event.node);
    }
    if (event.colDef.field === 'currency') {
        const code = event.newValue;
        if (code && config && config.rates && config.rates[code]) {
            event.node.setDataValue('exchange_rate', config.rates[code]);
        }
    }
}

function buildColumnDefs() {
    const selectEditor = {
        cellEditor: 'agSelectCellEditor',
        cellEditorParams: { values: [] },
    };

    return [
        {
            field: 'actions',
            headerName: 'Actions',
            width: 160,
            sortable: false,
            filter: false,
            editable: false,
            cellRenderer: ActionsRenderer,
            pinned: 'left',
        },
        {
            field: 'charges',
            headerName: 'Charges',
            width: 260,
            ...selectEditor,
            cellEditorParams: { values: config ? config.charges : [] },
        },
        {
            field: 'type',
            headerName: 'Type',
            width: 200,
            ...selectEditor,
            cellEditorParams: { values: config ? config.types : [] },
        },
        { field: 'm_r_number', headerName: 'M & R #', width: 200 },
        { field: 'bl_number', headerName: 'BL #', width: 200 },
        { field: 'container_number', headerName: 'Container #', width: 240 },
        {
            field: 'size',
            headerName: 'Size',
            width: 160,
            ...selectEditor,
            cellEditorParams: { values: config ? config.sizes : [] },
        },
        {
            field: 'size_type',
            headerName: 'Type',
            width: 220,
            ...selectEditor,
            cellEditorParams: { values: config ? config.containerTypes : [] },
        },
        { field: 'amount', headerName: 'Amount', width: 200 },
        {
            field: 'currency',
            headerName: 'Currency',
            width: 180,
            ...selectEditor,
            cellEditorParams: { values: config ? config.currencyCodes : [] },
        },
        { field: 'exchange_rate', headerName: 'Exch. Rate', width: 180 },
        { field: 'amount_dollar', headerName: 'Amount ($)', width: 200, editable: false },
        { field: 'vat_percentage', headerName: 'VAT %', width: 140, type: 'rightAligned' },
        { field: 'vat_amount', headerName: 'VAT Amt', width: 180, type: 'rightAligned', editable: false },
        { field: 'vat_amount_dollar', headerName: 'VAT Amt ($)', width: 200, type: 'rightAligned', editable: false },
        { field: 'remarks', headerName: 'Remarks', width: 260 },
    ];
}

export function initPurchaseInvoiceGrid(el, cfg) {
    config = cfg;

    try {
        gridApi = createGrid(el, {
            theme: 'legacy',
            columnDefs: buildColumnDefs(),
            rowData: cfg.details || [],
            defaultColDef: {
                editable: true,
                resizable: true,
                sortable: true,
                filter: true,
                minWidth: 70,
            },
            domLayout: 'normal',
            animateRows: true,
            onCellValueChanged,
            stopEditingWhenCellsLoseFocus: true,
            suppressRowClickSelection: true,
            rowSelection: 'single',
            getRowId: (params) => {
                if (params.data.id) return String(params.data.id);
                if (params.data._rowId) return params.data._rowId;
                rowCounter++;
                params.data._rowId = 'new_' + rowCounter;
                return params.data._rowId;
            },
        });
    } catch (e) {
        console.error('agGrid init error:', e);
    }
}

export function setGridData(details) {
    if (!gridApi) return;
    gridApi.setGridOption('rowData', details || []);
}

export function addRow() {
    if (!gridApi) return;
    gridApi.applyTransaction({ add: [{}] });
}

export function getRowData() {
    if (!gridApi) return [];
    const rows = [];
    gridApi.forEachNode((node) => rows.push(node.data));
    return rows;
}

export async function saveRow(params) {
    if (!gridApi || !config) return;

    const node = params.node;
    const data = { ...node.data };
    const detailId = data.id;
    delete data.id;
    delete data._rowId;

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const url = detailId
        ? `/purchase-invoices/${config.invoiceId}/details/${detailId}`
        : `/purchase-invoices/${config.invoiceId}/details`;
    const method = detailId ? 'PATCH' : 'POST';

    try {
        const resp = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
            body: JSON.stringify(data),
        });
        const result = await resp.json();

        if (resp.ok) {
            if (!detailId && result.detail) {
                node.setData({ ...node.data, id: result.detail.id });
            }
            flashRow(node);
            window.Alerts.toast('Detail saved.');
        } else {
            window.Alerts.error(result.message || 'Failed to save');
        }
    } catch (e) {
        window.Alerts.error('Network error: ' + e.message);
    }
}

export async function deleteRow(params) {
    if (!gridApi || !config) return;

    const node = params.node;
    const detailId = node.data.id;

    if (!detailId) {
        gridApi.applyTransaction({ remove: [node.data] });
        return;
    }

    const confirmed = await window.Alerts.confirm({
        title: 'Delete detail row?',
        text: 'This will permanently delete this detail row.',
        confirmText: 'Yes, delete it!',
    });
    if (!confirmed) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';

    try {
        const resp = await fetch(`/purchase-invoices/${config.invoiceId}/details/${detailId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
            },
        });
        if (resp.ok) {
            gridApi.applyTransaction({ remove: [node.data] });
            window.Alerts.toast('Detail deleted.');
        } else {
            const result = await resp.json();
            window.Alerts.error(result.message || 'Failed to delete');
        }
    } catch (e) {
        window.Alerts.error('Network error: ' + e.message);
    }
}

function flashRow(node) {
    const rowEl = document.querySelector(`[row-id="${node.id}"]`);
    if (rowEl) {
        rowEl.style.transition = 'background-color 0.3s';
        rowEl.style.backgroundColor = '#f0fdf4';
        setTimeout(() => { rowEl.style.backgroundColor = ''; }, 1500);
    }
}

window.PIG = {
    init: initPurchaseInvoiceGrid,
    setData: setGridData,
    addRow,
    saveRow,
    deleteRow,
    getRowData,
};
