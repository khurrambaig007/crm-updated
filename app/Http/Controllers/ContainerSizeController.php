<?php

namespace App\Http\Controllers;

use App\DataTables\ContainerSizesDataTable;
use App\Http\Requests\ContainerSize\StoreContainerSizeRequest;
use App\Http\Requests\ContainerSize\UpdateContainerSizeRequest;
use App\Models\ContainerSize;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContainerSizeController extends Controller
{
    public function index(Request $request, ContainerSizesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('container-sizes.index');
    }

    public function create(): View
    {
        return view('container-sizes.create');
    }

    public function store(StoreContainerSizeRequest $request): RedirectResponse
    {
        ContainerSize::create($request->validated());

        return redirect()->route('container-sizes.index')->with('status', 'Container size created successfully.');
    }

    public function edit(ContainerSize $containerSize): View
    {
        return view('container-sizes.edit', [
            'containerSize' => $containerSize,
        ]);
    }

    public function update(UpdateContainerSizeRequest $request, ContainerSize $containerSize): RedirectResponse
    {
        $containerSize->update($request->validated());

        return redirect()->route('container-sizes.index')->with('status', 'Container size updated successfully.');
    }

    public function destroy(Request $request, ContainerSize $containerSize): RedirectResponse
    {
        try {
            $containerSize->delete();
        } catch (QueryException $e) {
            return redirect()->route('container-sizes.index')->with('error', 'This container size is in use and cannot be deleted.');
        }

        return redirect()->route('container-sizes.index')->with('status', 'Container size deleted successfully.');
    }
}
