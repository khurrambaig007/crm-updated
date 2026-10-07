<div id="bank-account-quick-create-modal" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-slate-900/45 p-3 backdrop-blur-sm sm:p-6" role="dialog" aria-modal="true" aria-labelledby="bank-account-quick-create-title">
    <div class="mx-auto my-2 w-full max-w-2xl rounded-3xl bg-white p-6 shadow-2xl sm:my-6 sm:p-8">
        <div class="mb-5 flex items-start justify-between">
            <div>
                <h2 id="bank-account-quick-create-title" class="text-xl font-semibold text-gray-800">Add bank account</h2>
                <p class="mt-1 text-sm text-gray-500">The saved account is selected for this invoice automatically.</p>
            </div>
            <button type="button" data-modal-close class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600" title="Close">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
        </div>

        {!! html()->form('POST', route('bank-accounts.quick-create'))->id('bank-account-quick-create-form')->attributes(['data-submit-url' => route('bank-accounts.quick-create')])->class('space-y-5')->open() !!}
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div>
                    <label for="quick_bank" class="block text-sm font-medium text-topbar-text">Bank</label>
                    <div class="relative mt-1">
                        <select name="bank" id="quick_bank" class="block w-full appearance-none rounded-md border-0 bg-card-bg px-3 py-2 pr-8 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border focus:ring-2 focus:ring-inset focus:ring-primary-500">
                            <option value="">Select bank</option>
                            @foreach (config('dropdowns.bank_accounts.bank', []) as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-topbar-muted">⌄</div>
                    </div>
                </div>

                <div>
                    <label for="quick_beneficiary_name" class="block text-sm font-medium text-topbar-text">Beneficiary Name <span class="text-red-500">*</span></label>
                    <input type="text" name="beneficiary_name" id="quick_beneficiary_name" class="mt-1 block w-full rounded-md border-0 bg-card-bg px-3 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border" required>
                </div>

                <div>
                    <label for="quick_bank_name" class="block text-sm font-medium text-topbar-text">Bank Name</label>
                    <input type="text" name="bank_name" id="quick_bank_name" class="mt-1 block w-full rounded-md border-0 bg-card-bg px-3 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border">
                </div>

                <div>
                    <label for="quick_account" class="block text-sm font-medium text-topbar-text">Account <span class="text-red-500">*</span></label>
                    <input type="text" name="account" id="quick_account" class="mt-1 block w-full rounded-md border-0 bg-card-bg px-3 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border" required>
                </div>

                <div>
                    <label for="quick_iban" class="block text-sm font-medium text-topbar-text">IBAN</label>
                    <input type="text" name="iban" id="quick_iban" class="mt-1 block w-full rounded-md border-0 bg-card-bg px-3 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border">
                </div>

                <div>
                    <label for="quick_swift" class="block text-sm font-medium text-topbar-text">SWIFT</label>
                    <input type="text" name="swift" id="quick_swift" class="mt-1 block w-full rounded-md border-0 bg-card-bg px-3 py-2 text-sm text-topbar-text shadow-sm ring-1 ring-inset ring-card-border">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" data-modal-close class="rounded-lg px-4 py-2 text-sm font-medium text-topbar-muted hover:text-topbar-text">Cancel</button>
                <button type="submit" class="rounded-lg bg-primary-900 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-primary-700">Save</button>
            </div>
        {!! html()->form()->close() !!}
    </div>
</div>