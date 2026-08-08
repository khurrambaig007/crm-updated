<?php

namespace App\Http\Controllers;

use App\DataTables\CarriersDataTable;
use App\Http\Requests\Carrier\StoreCarrierRequest;
use App\Http\Requests\Carrier\UpdateCarrierRequest;
use App\Models\Carrier;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CarrierController extends Controller
{
    public function index(Request $request, CarriersDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('carriers.index');
    }

    public function create(): View
    {
        return view('carriers.create');
    }

    public function store(StoreCarrierRequest $request): RedirectResponse
    {
        Carrier::create($request->validated());

        return redirect()->route('carriers.index')->with('status', 'Carrier created successfully.');
    }

    public function edit(Carrier $carrier): View
    {
        return view('carriers.edit', [
            'carrier' => $carrier,
        ]);
    }

    public function update(UpdateCarrierRequest $request, Carrier $carrier): RedirectResponse
    {
        $carrier->update($request->validated());

        return redirect()->route('carriers.index')->with('status', 'Carrier updated successfully.');
    }

    public function destroy(Request $request, Carrier $carrier): RedirectResponse
    {
        try {
            $carrier->delete();
        } catch (QueryException $e) {
            return redirect()->route('carriers.index')->with('error', 'This carrier is in use and cannot be deleted.');
        }

        return redirect()->route('carriers.index')->with('status', 'Carrier deleted successfully.');
    }
}
