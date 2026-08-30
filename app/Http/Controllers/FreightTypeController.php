<?php

namespace App\Http\Controllers;

use App\DataTables\FreightTypesDataTable;
use App\Http\Requests\FreightType\StoreFreightTypeRequest;
use App\Http\Requests\FreightType\UpdateFreightTypeRequest;
use App\Models\FreightType;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FreightTypeController extends Controller
{
    public function index(Request $request, FreightTypesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('freight-types.index');
    }

    public function create(): View
    {
        return view('freight-types.create');
    }

    public function store(StoreFreightTypeRequest $request): RedirectResponse
    {
        FreightType::create($request->validated());

        return redirect()
            ->route('freight-types.index')
            ->with('status', 'Freight type created successfully.');
    }

    public function edit(FreightType $freightType): View
    {
        return view('freight-types.edit', [
            'freightType' => $freightType,
        ]);
    }

    public function update(UpdateFreightTypeRequest $request, FreightType $freightType): RedirectResponse
    {
        $freightType->update($request->validated());

        return redirect()
            ->route('freight-types.index')
            ->with('status', 'Freight type updated successfully.');
    }

    public function destroy(FreightType $freightType): RedirectResponse
    {
        try {
            $freightType->delete();
        } catch (QueryException $e) {
            return redirect()
                ->route('freight-types.index')
                ->with('error', 'This freight type is in use and cannot be deleted.');
        }

        return redirect()
            ->route('freight-types.index')
            ->with('status', 'Freight type deleted successfully.');
    }
}
