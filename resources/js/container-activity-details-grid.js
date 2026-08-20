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
    this.saveBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8 15 8"/></svg>';

    this.delBtn = document.createElement('button');
    this.delBtn.type = 'button';
    this.delBtn.className = 'inline-flex items-center justify-center rounded p-1 text-red-500 hover:bg-red-50';
    this.delBtn.title = 'Delete';
    this.delBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a4 2 0 0 1 2 2v2"/></svg>';

    // Look up the click handlers via the window global namespace. Using a
    // property name unique to this module ('__cad_save' etc.) ensures the
    // bundler/minifier cannot alias it to the property name on another
    // module's window object (e.g. window.__pig_save). Direct property
    // access on a global object survives module merging.
    this.saveBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        window['__cad_save'](this);
    });
    this.delBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        window['__cad_delete'](this);
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

/**
 * When the user picks a Vessel in any TS column, auto-fill the adjacent
 * Voyage column with the voyage_number from the matching vessel_voyages row.
 * Vessels are exposed to the grid as a flat list of vessel_name strings (the
 * shape agSelectCellEditor wants), with a separate lookup map for voyage.
 */
function onCellValueChanged(event) {
    const field = event.colDef.field;
    if (
        field === 'vessel_ts1' ||
        field === 'vessel_ts2' ||
        field === 'vessel_ts3'
    ) {
        const voyageField = field.replace('vessel_', 'voyage_');
        const lookup = config && config.vesselVoyageLookup ? config.vesselVoyageLookup : {};
        event.node.setDataValue(voyageField, lookup[event.newValue] || '');
    }
}

function buildColumnDefs() {
    const vesselOptions = config ? config.vesselNames : [];

    const select = (values) => ({
        cellEditor: 'agSelectCellEditor',
        cellEditorParams: { values },
    });

    return [
        {
            field: 'actions',
            headerName: 'Actions',
            width: 140,
            sortable: false,
            filter: false,
            editable: false,
            cellRenderer: ActionsRenderer,
            pinned: 'left',
        },
        { field: 'containe_no',    headerName: 'Container #',     width: 180 },
        { field: 'size_type',      headerName: 'Size/Type',        width: 140 },
        { field: 'principle',      headerName: 'Principle',        width: 160 },
        { field: 'bl_number',      headerName: 'BL #',             width: 160 },
        { field: 'booking_number', headerName: 'Booking #',        width: 160 },
        {
            field: 'status',
            headerName: 'Status',
            width: 180,
            ...select(config ? config.statuses : []),
        },
        {
            field: 'cargo_type',
            headerName: 'Cargo Type',
            width: 160,
            ...select(config ? config.cargoTypes : []),
        },
        {
            field: 'one_door_open',
            headerName: 'One Door Open',
            width: 150,
            ...select(['Yes', 'No']),
        },
        { field: 'last_activity', headerName: 'Last Activity',    width: 180 },
        { field: 'system_remarks', headerName: 'System Remarks',  width: 220 },
        {
            field: 'vessel_ts1',
            headerName: 'Vessel (TS1)',
            width: 180,
            ...select(vesselOptions),
        },
        { field: 'voyage_ts1',     headerName: 'Voyage (TS1)',     width: 160 },
        {
            field: 'sailing_date_ts1',
            headerName: 'Sailing Date (TS1)',
            width: 180,
            cellEditor: 'agDateStringCellEditor',
        },
        {
            field: 'vessel_ts2',
            headerName: 'Vessel (TS2)',
            width: 180,
            ...select(vesselOptions),
        },
        { field: 'voyage_ts2',     headerName: 'Voyage (TS2)',     width: 160 },
        {
            field: 'sailing_date_ts2',
            headerName: 'Sailing Date (TS2)',
            width: 180,
            cellEditor: 'agDateStringCellEditor',
        },
        {
            field: 'vessel_ts3',
            headerName: 'Vessel (TS3)',
            width: 180,
            ...select(vesselOptions),
        },
        { field: 'voyage_ts3',     headerName: 'Voyage (TS3)',     width: 160 },
        {
            field: 'sailing_date_ts3',
            headerName: 'Sailing Date (TS3)',
            width: 180,
            cellEditor: 'agDateStringCellEditor',
        },
    ];
}

export function initContainerActivityDetailsGrid(el, cfg) {
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

export async function saveContainerActivityDetailRow(rendererOrParams) {
    if (!gridApi || !config) return;

    // Accept either the renderer instance (from the click handler) or a
    // plain ICellRendererParams object (e.g. when called from outside the
    // grid). Either way, params.node must be reachable.
    const params = rendererOrParams && rendererOrParams.params
        ? rendererOrParams.params
        : rendererOrParams;
    const node = params && params.node;
    if (!node) {
        console.warn('saveContainerActivityDetailRow: missing node', rendererOrParams);
        return;
    }

    const data = { ...node.data };
    const detailId = data.id;
    delete data.id;
    delete data._rowId;

    const token = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const url = detailId
        ? `/container-activities/${config.activityId}/details/${detailId}`
        : `/container-activities/${config.activityId}/details`;
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

export async function deleteContainerActivityDetailRow(rendererOrParams) {
    if (!gridApi || !config) return;

    const params = rendererOrParams && rendererOrParams.params
        ? rendererOrParams.params
        : rendererOrParams;
    const node = params && params.node;
    if (!node) {
        console.warn('deleteContainerActivityDetailRow: missing node', rendererOrParams);
        return;
    }

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
        const resp = await fetch(`/container-activities/${config.activityId}/details/${detailId}`, {
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

window.CAD = {
    init: initContainerActivityDetailsGrid,
    setData: setGridData,
    addRow,
    getRowData,
};

// Populate the unique window property handlers — see ActionsRenderer for
// why these names are prefixed to defeat bundler cross-module aliasing.
window['__cad_save'] = saveContainerActivityDetailRow;
window['__cad_delete'] = deleteContainerActivityDetailRow;
