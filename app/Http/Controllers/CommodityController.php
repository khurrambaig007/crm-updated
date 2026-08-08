<?php

namespace App\Http\Controllers;

use App\DataTables\CommoditiesDataTable;
use App\Http\Requests\Commodity\StoreCommodityRequest;
use App\Http\Requests\Commodity\UpdateCommodityRequest;
use App\Models\Commodity;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommodityController extends Controller
{
    public function index(Request $request, CommoditiesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('commodities.index');
    }

    public function create(): View
    {
        return view('commodities.create');
    }

    public function store(StoreCommodityRequest $request): RedirectResponse
    {
        Commodity::create($request->validated());

        return redirect()->route('commodities.index')->with('status', 'Commodity created successfully.');
    }

    public function edit(Commodity $commodity): View
    {
        return view('commodities.edit', [
            'commodity' => $commodity,
        ]);
    }

    public function update(UpdateCommodityRequest $request, Commodity $commodity): RedirectResponse
    {
        $commodity->update($request->validated());

        return redirect()->route('commodities.index')->with('status', 'Commodity updated successfully.');
    }

    public function destroy(Request $request, Commodity $commodity): RedirectResponse
    {
        try {
            $commodity->delete();
        } catch (QueryException $e) {
            return redirect()->route('commodities.index')->with('error', 'This commodity is in use and cannot be deleted.');
        }

        return redirect()->route('commodities.index')->with('status', 'Commodity deleted successfully.');
    }
}
