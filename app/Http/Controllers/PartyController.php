<?php

namespace App\Http\Controllers;

use App\DataTables\PartiesDataTable;
use App\Http\Requests\Party\StorePartyRequest;
use App\Http\Requests\Party\UpdatePartyRequest;
use App\Models\Agent;
use App\Models\Party;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PartyController extends Controller
{
    public function index(Request $request, PartiesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('parties.index');
    }

    public function create(): View
    {
        return view('parties.create', [
            'agents' => Agent::orderBy('code')->get(),
        ]);
    }

    public function store(StorePartyRequest $request): RedirectResponse
    {
        Party::create($request->validated());

        return redirect()->route('parties.index')->with('status', 'Party created successfully.');
    }

    public function edit(Party $party): View
    {
        return view('parties.edit', [
            'party' => $party,
            'agents' => Agent::orderBy('code')->get(),
        ]);
    }

    public function update(UpdatePartyRequest $request, Party $party): RedirectResponse
    {
        $party->update($request->validated());

        return redirect()->route('parties.index')->with('status', 'Party updated successfully.');
    }

    public function destroy(Request $request, Party $party): RedirectResponse
    {
        try {
            $party->delete();
        } catch (QueryException $e) {
            return redirect()->route('parties.index')->with('error', 'This party is in use and cannot be deleted.');
        }

        return redirect()->route('parties.index')->with('status', 'Party deleted successfully.');
    }
}
