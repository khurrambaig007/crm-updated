<?php

namespace App\Http\Controllers;

use App\DataTables\SettlementTypesDataTable;
use App\Http\Requests\SettlementType\StoreSettlementTypeRequest;
use App\Http\Requests\SettlementType\UpdateSettlementTypeRequest;
use App\Models\SettlementType;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettlementTypeController extends Controller
{
    public function index(Request $request, SettlementTypesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('settlement-types.index');
    }

    public function create(): View
    {
        return view('settlement-types.create', [
            'nextNumber' => $this->nextSettlementNumber(),
        ]);
    }

    public function store(StoreSettlementTypeRequest $request): RedirectResponse
    {
        SettlementType::create([
            ...$request->validated(),
            'number' => $this->nextSettlementNumber(),
        ]);

        return redirect()->route('settlement-types.index')->with('status', 'Settlement type created successfully.');
    }

    public function edit(SettlementType $settlementType): View
    {
        return view('settlement-types.edit', [
            'settlementType' => $settlementType,
        ]);
    }

    public function update(UpdateSettlementTypeRequest $request, SettlementType $settlementType): RedirectResponse
    {
        $settlementType->update($request->validated());

        return redirect()->route('settlement-types.index')->with('status', 'Settlement type updated successfully.');
    }

    public function destroy(Request $request, SettlementType $settlementType): RedirectResponse
    {
        try {
            $settlementType->delete();
        } catch (QueryException $e) {
            return redirect()->route('settlement-types.index')->with('error', 'This settlement type is in use and cannot be deleted.');
        }

        return redirect()->route('settlement-types.index')->with('status', 'Settlement type deleted successfully.');
    }

    private function nextSettlementNumber(): string
    {
        $max = (int) SettlementType::max('number');

        return str_pad((string) ($max + 1), 10, '0', STR_PAD_LEFT);
    }
}
