<?php

namespace App\Http\Controllers;

use App\DataTables\VesselVoyagesDataTable;
use App\Http\Requests\VesselVoyage\StoreVesselVoyageRequest;
use App\Http\Requests\VesselVoyage\UpdateVesselVoyageRequest;
use App\Models\VesselVoyage;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VesselVoyageController extends Controller
{
    public function index(Request $request, VesselVoyagesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('vessel-voyages.index');
    }

    public function create(): View
    {
        return view('vessel-voyages.create');
    }

    public function store(StoreVesselVoyageRequest $request): RedirectResponse
    {
        VesselVoyage::create($request->validated());

        return redirect()->route('vessel-voyages.index')->with('status', 'Vessel voyage created successfully.');
    }

    public function edit(VesselVoyage $vesselVoyage): View
    {
        return view('vessel-voyages.edit', [
            'vesselVoyage' => $vesselVoyage,
        ]);
    }

    public function update(UpdateVesselVoyageRequest $request, VesselVoyage $vesselVoyage): RedirectResponse
    {
        $vesselVoyage->update($request->validated());

        return redirect()->route('vessel-voyages.index')->with('status', 'Vessel voyage updated successfully.');
    }

    public function destroy(Request $request, VesselVoyage $vesselVoyage): RedirectResponse
    {
        try {
            $vesselVoyage->delete();
        } catch (QueryException $e) {
            return redirect()->route('vessel-voyages.index')->with('error', 'This vessel voyage is in use and cannot be deleted.');
        }

        return redirect()->route('vessel-voyages.index')->with('status', 'Vessel voyage deleted successfully.');
    }
}
