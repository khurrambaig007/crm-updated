<?php

namespace App\Http\Controllers;

use App\DataTables\SubCompaniesDataTable;
use App\Http\Requests\SubCompany\StoreSubCompanyRequest;
use App\Http\Requests\SubCompany\UpdateSubCompanyRequest;
use App\Models\SubCompany;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubCompanyController extends Controller
{
    public function index(Request $request, SubCompaniesDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('sub-companies.index');
    }

    public function create(): View
    {
        return view('sub-companies.create');
    }

    public function store(StoreSubCompanyRequest $request): RedirectResponse
    {
        SubCompany::create($request->validated());

        return redirect()->route('sub-companies.index')->with('status', 'Sub company created successfully.');
    }

    public function edit(SubCompany $subCompany): View
    {
        return view('sub-companies.edit', [
            'subCompany' => $subCompany,
        ]);
    }

    public function update(UpdateSubCompanyRequest $request, SubCompany $subCompany): RedirectResponse
    {
        $subCompany->update($request->validated());

        return redirect()->route('sub-companies.index')->with('status', 'Sub company updated successfully.');
    }

    public function destroy(Request $request, SubCompany $subCompany): RedirectResponse
    {
        try {
            $subCompany->delete();
        } catch (QueryException $e) {
            return redirect()->route('sub-companies.index')->with('error', 'This sub company is in use and cannot be deleted.');
        }

        return redirect()->route('sub-companies.index')->with('status', 'Sub company deleted successfully.');
    }
}
