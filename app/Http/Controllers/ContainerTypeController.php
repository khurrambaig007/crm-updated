<?php

namespace App\Http\Controllers;

use App\DataTables\ContainerTypesDataTable;
use App\Http\Requests\ContainerType\StoreContainerTypeRequest;
use App\Http\Requests\ContainerType\UpdateContainerTypeRequest;
use App\Models\ContainerType;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContainerTypeController extends Controller
{
    public function index(Request $request, ContainerTypesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('container-types.index');
    }

    public function create(): View
    {
        return view('container-types.create');
    }

    public function store(StoreContainerTypeRequest $request): RedirectResponse
    {
        ContainerType::create($request->validated());

        return redirect()->route('container-types.index')->with('status', 'Container type created successfully.');
    }

    public function edit(ContainerType $containerType): View
    {
        return view('container-types.edit', [
            'containerType' => $containerType,
        ]);
    }

    public function update(UpdateContainerTypeRequest $request, ContainerType $containerType): RedirectResponse
    {
        $containerType->update($request->validated());

        return redirect()->route('container-types.index')->with('status', 'Container type updated successfully.');
    }

    public function destroy(Request $request, ContainerType $containerType): RedirectResponse
    {
        try {
            $containerType->delete();
        } catch (QueryException $e) {
            return redirect()->route('container-types.index')->with('error', 'This container type is in use and cannot be deleted.');
        }

        return redirect()->route('container-types.index')->with('status', 'Container type deleted successfully.');
    }
}
