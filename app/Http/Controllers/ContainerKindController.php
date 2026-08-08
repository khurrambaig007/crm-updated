<?php

namespace App\Http\Controllers;

use App\DataTables\ContainerKindsDataTable;
use App\Http\Requests\ContainerKind\StoreContainerKindRequest;
use App\Http\Requests\ContainerKind\UpdateContainerKindRequest;
use App\Models\ContainerKind;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContainerKindController extends Controller
{
    public function index(Request $request, ContainerKindsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('container-kinds.index');
    }

    public function create(): View
    {
        return view('container-kinds.create');
    }

    public function store(StoreContainerKindRequest $request): RedirectResponse
    {
        ContainerKind::create($request->validated());

        return redirect()->route('container-kinds.index')->with('status', 'Container kind created successfully.');
    }

    public function edit(ContainerKind $containerKind): View
    {
        return view('container-kinds.edit', [
            'containerKind' => $containerKind,
        ]);
    }

    public function update(UpdateContainerKindRequest $request, ContainerKind $containerKind): RedirectResponse
    {
        $containerKind->update($request->validated());

        return redirect()->route('container-kinds.index')->with('status', 'Container kind updated successfully.');
    }

    public function destroy(Request $request, ContainerKind $containerKind): RedirectResponse
    {
        try {
            $containerKind->delete();
        } catch (QueryException $e) {
            return redirect()->route('container-kinds.index')->with('error', 'This container kind is in use and cannot be deleted.');
        }

        return redirect()->route('container-kinds.index')->with('status', 'Container kind deleted successfully.');
    }
}
