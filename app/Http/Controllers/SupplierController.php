<?php

namespace App\Http\Controllers;

use App\DataTables\SuppliersDataTable;
use App\Http\Requests\Supplier\StoreSupplierRequest;
use App\Http\Requests\Supplier\UpdateSupplierRequest;
use App\Models\Pol;
use App\Models\Supplier;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(Request $request, SuppliersDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('suppliers.index');
    }

    public function create(): View
    {
        return view('suppliers.create', [
            'pols' => Pol::orderBy('city')->get(),
        ]);
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        Supplier::create($request->validated());

        return redirect()->route('suppliers.index')->with('status', 'Supplier created successfully.');
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', [
            'supplier' => $supplier,
            'pols' => Pol::orderBy('city')->get(),
        ]);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()->route('suppliers.index')->with('status', 'Supplier updated successfully.');
    }

    public function destroy(Request $request, Supplier $supplier): RedirectResponse
    {
        try {
            $supplier->delete();
        } catch (QueryException $e) {
            return redirect()->route('suppliers.index')->with('error', 'This supplier is in use and cannot be deleted.');
        }

        return redirect()->route('suppliers.index')->with('status', 'Supplier deleted successfully.');
    }
}
