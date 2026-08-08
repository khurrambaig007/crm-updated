<?php

namespace App\Http\Controllers;

use App\DataTables\PolsDataTable;
use App\Http\Requests\Pol\StorePolRequest;
use App\Http\Requests\Pol\UpdatePolRequest;
use App\Models\ContainerSize;
use App\Models\Pol;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PolController extends Controller
{
    public function index(Request $request, PolsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('port-locations.index');
    }

    public function create(): View
    {
        return view('port-locations.create', [
            'containerSizes' => ContainerSize::orderBy('size')->get(),
        ]);
    }

    public function store(StorePolRequest $request): RedirectResponse
    {
        Pol::create($request->validated());

        return redirect()->route('port-locations.index')->with('status', 'Port location created successfully.');
    }

    public function edit(Pol $pol): View
    {
        return view('port-locations.edit', [
            'pol' => $pol,
            'containerSizes' => ContainerSize::orderBy('size')->get(),
        ]);
    }

    public function update(UpdatePolRequest $request, Pol $pol): RedirectResponse
    {
        $pol->update($request->validated());

        return redirect()->route('port-locations.index')->with('status', 'Port location updated successfully.');
    }

    public function destroy(Request $request, Pol $pol): RedirectResponse
    {
        try {
            $pol->delete();
        } catch (QueryException $e) {
            return redirect()->route('port-locations.index')->with('error', 'This port location is in use and cannot be deleted.');
        }

        return redirect()->route('port-locations.index')->with('status', 'Port location deleted successfully.');
    }
}
