<?php

namespace App\Http\Controllers;

use App\DataTables\MaintenanceRepairEntriesDataTable;
use App\Http\Requests\MaintenanceRepairEntry\StoreMaintenanceRepairEntryRequest;
use App\Http\Requests\MaintenanceRepairEntry\UpdateMaintenanceRepairEntryRequest;
use App\Models\Agent;
use App\Models\MaintenanceRepairEntry;
use App\Models\Party;
use App\Models\Supplier;
use App\Models\VesselVoyage;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MaintenanceRepairEntryController extends Controller
{
    public function index(Request $request, MaintenanceRepairEntriesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('maintenance-repair-entries.index');
    }

    public function create(): View
    {
        return view('maintenance-repair-entries.create', [
            'agents' => Agent::orderBy('code')->get(),
            'vesselVoyages' => VesselVoyage::orderBy('vessel_name')->get(),
            'liableParties' => Party::orderBy('name')->get(),
            'vendors' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function store(StoreMaintenanceRepairEntryRequest $request): RedirectResponse
    {
        MaintenanceRepairEntry::create($request->validated());

        return redirect()->route('maintenance-repair-entries.index')->with('status', 'Maintenance & Repair entry created successfully.');
    }

    public function edit(MaintenanceRepairEntry $maintenanceRepairEntry): View
    {
        return view('maintenance-repair-entries.edit', [
            'entry' => $maintenanceRepairEntry,
            'agents' => Agent::orderBy('code')->get(),
            'vesselVoyages' => VesselVoyage::orderBy('vessel_name')->get(),
            'liableParties' => Party::orderBy('name')->get(),
            'vendors' => Supplier::orderBy('name')->get(),
        ]);
    }

    public function update(UpdateMaintenanceRepairEntryRequest $request, MaintenanceRepairEntry $maintenanceRepairEntry): RedirectResponse
    {
        $maintenanceRepairEntry->update($request->validated());

        return redirect()->route('maintenance-repair-entries.index')->with('status', 'Maintenance & Repair entry updated successfully.');
    }

    public function destroy(Request $request, MaintenanceRepairEntry $maintenanceRepairEntry): RedirectResponse
    {
        try {
            $maintenanceRepairEntry->delete();
        } catch (QueryException $e) {
            return redirect()->route('maintenance-repair-entries.index')->with('error', 'This entry is in use and cannot be deleted.');
        }

        return redirect()->route('maintenance-repair-entries.index')->with('status', 'Maintenance & Repair entry deleted successfully.');
    }
}
