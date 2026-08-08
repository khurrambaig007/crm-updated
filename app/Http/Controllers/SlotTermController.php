<?php

namespace App\Http\Controllers;

use App\DataTables\SlotTermsDataTable;
use App\Http\Requests\SlotTerm\StoreSlotTermRequest;
use App\Http\Requests\SlotTerm\UpdateSlotTermRequest;
use App\Models\SlotTerm;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SlotTermController extends Controller
{
    public function index(Request $request, SlotTermsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('slots.index');
    }

    public function create(): View
    {
        return view('slots.create');
    }

    public function store(StoreSlotTermRequest $request): RedirectResponse
    {
        SlotTerm::create($request->validated());

        return redirect()->route('slots.index')->with('status', 'Slot term created successfully.');
    }

    public function edit(SlotTerm $slotTerm): View
    {
        return view('slots.edit', [
            'slotTerm' => $slotTerm,
        ]);
    }

    public function update(UpdateSlotTermRequest $request, SlotTerm $slotTerm): RedirectResponse
    {
        $slotTerm->update($request->validated());

        return redirect()->route('slots.index')->with('status', 'Slot term updated successfully.');
    }

    public function destroy(Request $request, SlotTerm $slotTerm): RedirectResponse
    {
        try {
            $slotTerm->delete();
        } catch (QueryException $e) {
            return redirect()->route('slots.index')->with('error', 'This slot term is in use and cannot be deleted.');
        }

        return redirect()->route('slots.index')->with('status', 'Slot term deleted successfully.');
    }
}
