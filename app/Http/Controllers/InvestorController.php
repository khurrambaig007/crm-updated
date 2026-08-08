<?php

namespace App\Http\Controllers;

use App\DataTables\InvestorsDataTable;
use App\Http\Requests\Investor\StoreInvestorRequest;
use App\Http\Requests\Investor\UpdateInvestorRequest;
use App\Models\Investor;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvestorController extends Controller
{
    public function index(Request $request, InvestorsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('investors.index');
    }

    public function create(): View
    {
        return view('investors.create');
    }

    public function store(StoreInvestorRequest $request): RedirectResponse
    {
        Investor::create($request->validated());

        return redirect()->route('investors.index')->with('status', 'Investor created successfully.');
    }

    public function edit(Investor $investor): View
    {
        return view('investors.edit', [
            'investor' => $investor,
        ]);
    }

    public function update(UpdateInvestorRequest $request, Investor $investor): RedirectResponse
    {
        $investor->update($request->validated());

        return redirect()->route('investors.index')->with('status', 'Investor updated successfully.');
    }

    public function destroy(Request $request, Investor $investor): RedirectResponse
    {
        try {
            $investor->delete();
        } catch (QueryException $e) {
            return redirect()->route('investors.index')->with('error', 'This investor is in use and cannot be deleted.');
        }

        return redirect()->route('investors.index')->with('status', 'Investor deleted successfully.');
    }
}
