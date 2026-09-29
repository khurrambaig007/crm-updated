<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankAccount\SaveBankAccountRequest;
use App\Models\BankAccount;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BankAccountController extends Controller
{
    public function edit(): View
    {
        $account = BankAccount::current();

        return view('bank-accounts.edit', [
            'account' => $account,
            'isNew' => ! $account->exists,
        ]);
    }

    public function store(SaveBankAccountRequest $request): RedirectResponse
    {
        // The bank account is a singleton, so a second concurrent submission must
        // never create a duplicate row.
        if (BankAccount::hasAccount()) {
            return redirect()->route('bank-accounts.index')
                ->with('error', 'A bank account already exists.');
        }

        $account = new BankAccount;
        $this->fillAccount($account, $request);
        $account->save();

        return redirect()->route('bank-accounts.index')
            ->with('status', 'Bank account created successfully.');
    }

    public function update(SaveBankAccountRequest $request): RedirectResponse
    {
        $account = BankAccount::current();

        if (! $account->exists) {
            return redirect()->route('bank-accounts.index')
                ->with('error', 'No bank account has been created yet.');
        }

        $this->fillAccount($account, $request);
        $account->save();

        return redirect()->route('bank-accounts.index')
            ->with('status', 'Bank account updated successfully.');
    }

    /**
     * Apply the validated scalar and repeatable fields to the bank account.
     */
    private function fillAccount(BankAccount $account, SaveBankAccountRequest $request): void
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
