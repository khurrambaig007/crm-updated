<?php

namespace App\Http\Controllers;

use App\DataTables\BankAccountsDataTable;
use App\Http\Requests\BankAccount\QuickCreateBankAccountRequest;
use App\Http\Requests\BankAccount\SaveBankAccountRequest;
use App\Http\Requests\BankAccount\StoreBankAccountRequest;
use App\Http\Requests\BankAccount\UpdateBankAccountRequest;
use App\Models\BankAccount;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function index(Request $request, BankAccountsDataTable $dataTable): mixed
    {
        if ($request->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('bank-accounts.index');
    }

    public function create(): View
    {
        return view('bank-accounts.create');
    }

    public function store(StoreBankAccountRequest $request): RedirectResponse
    {
        $account = new BankAccount;
        self::applyAccount($account, $request);
        $account->save();

        return redirect()->route('bank-accounts.index')
            ->with('status', 'Bank account created successfully.');
    }

    /**
     * JSON-only create used by the quick-create modal on the sales invoice form;
     * returns the id and dropdown label of the saved account.
     */
    public function quickStore(QuickCreateBankAccountRequest $request): JsonResponse
    {
        $account = new BankAccount;
        self::applyAccount($account, $request);
        $account->save();

        return response()->json([
            'id' => $account->id,
            'label' => $account->optionLabel(),
        ], 201);
    }

    public function edit(BankAccount $bankAccount): View
    {
        return view('bank-accounts.edit', [
            'account' => $bankAccount,
            'isNew' => false,
        ]);
    }

    public function update(UpdateBankAccountRequest $request, BankAccount $bankAccount): RedirectResponse
    {
        self::applyAccount($bankAccount, $request);
        $bankAccount->save();

        return redirect()->route('bank-accounts.index')
            ->with('status', 'Bank account updated successfully.');
    }

    public function destroy(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        try {
            $bankAccount->delete();
        } catch (QueryException $e) {
            return redirect()->route('bank-accounts.index')
                ->with('error', 'This bank account is in use and cannot be deleted.');
        }

        return redirect()->route('bank-accounts.index')
            ->with('status', 'Bank account deleted successfully.');
    }

    /**
     * Apply the validated scalar and repeatable fields to the bank account.
     */
    public static function applyAccount(BankAccount $account, SaveBankAccountRequest $request): void
    {
        $account->fill($request->safe()->only([
            'bank',
            'beneficiary_name',
            'bank_name',
            'account',
            'iban',
            'swift',
            'address',
        ]));

        $account->custom_fields = BankAccount::normalizeCustomFields($request->input('custom_fields', []));
    }
}
