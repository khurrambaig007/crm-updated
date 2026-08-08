<?php

namespace App\Http\Controllers;

use App\DataTables\ChargesDataTable;
use App\Http\Requests\Charge\StoreChargeRequest;
use App\Http\Requests\Charge\UpdateChargeRequest;
use App\Models\Charge;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChargeController extends Controller
{
    public function index(Request $request, ChargesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('charges.index');
    }

    public function create(): View
    {
        return view('charges.create');
    }

    public function store(StoreChargeRequest $request): RedirectResponse
    {
        Charge::create($request->validated());

        return redirect()->route('charges.index')->with('status', 'Charge created successfully.');
    }

    public function edit(Charge $charge): View
    {
        return view('charges.edit', [
            'charge' => $charge,
        ]);
    }

    public function update(UpdateChargeRequest $request, Charge $charge): RedirectResponse
    {
        $charge->update($request->validated());

        return redirect()->route('charges.index')->with('status', 'Charge updated successfully.');
    }

    public function destroy(Request $request, Charge $charge): RedirectResponse
    {
        try {
            $charge->delete();
        } catch (QueryException $e) {
            return redirect()->route('charges.index')->with('error', 'This charge is in use and cannot be deleted.');
        }

        return redirect()->route('charges.index')->with('status', 'Charge deleted successfully.');
    }
}
