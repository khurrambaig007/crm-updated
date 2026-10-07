<?php

namespace App\Http\Controllers;

use App\DataTables\SalesInvoicesDataTable;
use App\Http\Requests\SalesInvoice\StoreSalesInvoiceRequest;
use App\Http\Requests\SalesInvoice\UpdateSalesInvoiceRequest;
use App\Models\BankAccount;
use App\Models\CompanyProfile;
use App\Models\Party;
use App\Models\SalesInvoice;
use App\Services\ExchangeRateService;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesInvoiceController extends Controller
{
    public function index(SalesInvoicesDataTable $dataTable): View
    {
        return $dataTable->render('sales-invoices.index');
    }

    public function getData(SalesInvoicesDataTable $dataTable): mixed
    {
        return $dataTable->ajax();
    }

    public function create(): View
    {
        $invoice = new SalesInvoice;
        $invoice->invoice_date = today();

        return view('sales-invoices.edit', $this->formData($invoice, true));
    }

    public function store(StoreSalesInvoiceRequest $request): RedirectResponse
    {
        $invoice = DB::transaction(function () use ($request): SalesInvoice {
            $validated = $request->validated();
            $details = $validated['details'];
            unset($validated['details']);

            $totals = $this->calculateTotals($details, (float) $validated['vat_rate']);
            $invoice = SalesInvoice::create(array_merge($validated, $totals, ['invoice_number' => null]));
            $invoice->details()->createMany($details);

            return $invoice;
        });

        return redirect()->route('sales-invoices.edit', $invoice)->with('status', 'Sales invoice created successfully.');
    }

    public function edit(SalesInvoice $salesInvoice): View
    {
        $salesInvoice->load(['party', 'details']);

        return view('sales-invoices.edit', $this->formData($salesInvoice, false));
    }

    public function update(UpdateSalesInvoiceRequest $request, SalesInvoice $salesInvoice): RedirectResponse
    {
        DB::transaction(function () use ($request, $salesInvoice): void {
            $validated = $request->validated();
            $details = $validated['details'];
            unset($validated['details']);

            $salesInvoice->update(array_merge(
                $validated,
                $this->calculateTotals($details, (float) $validated['vat_rate']),
            ));
            $salesInvoice->details()->delete();
            $salesInvoice->details()->createMany($details);
        });

        return redirect()->route('sales-invoices.edit', $salesInvoice)->with('status', 'Sales invoice updated successfully.');
    }

    public function destroy(SalesInvoice $salesInvoice): RedirectResponse
    {
        $salesInvoice->delete();

        return redirect()->route('sales-invoices.index')->with('status', 'Sales invoice deleted successfully.');
    }

    public function downloadPdf(SalesInvoice $salesInvoice): Response
    {
        return $this->buildPdf($salesInvoice)->download($salesInvoice->invoice_number.'.pdf');
    }

    public function viewPdf(SalesInvoice $salesInvoice): Response
    {
        // Inline disposition renders the PDF in the browser instead of triggering a download.
        return $this->buildPdf($salesInvoice)->stream($salesInvoice->invoice_number.'.pdf');
    }

    private function buildPdf(SalesInvoice $salesInvoice): PdfDocument
    {
        $salesInvoice->load(['party', 'details', 'bankAccount']);
        $profile = CompanyProfile::current();
        // Prefer the account chosen on the invoice; fall back to the first saved
        // account for invoices created before bank-account selection existed.
        $bankAccount = $salesInvoice->bankAccount ?? BankAccount::current();

        return Pdf::loadView('sales-invoices.pdf', [
            'invoice' => $salesInvoice,
            'company' => $profile,
            'bankAccount' => $bankAccount,
            'brandLogoPath' => $profile->logoPath(),
            'brandLogoSize' => $profile->logoDisplaySize(240, 125),
        ])->setPaper('a4', 'portrait');
    }

    /** @return array<string, mixed> */
    private function formData(SalesInvoice $invoice, bool $isNew): array
    {
        $invoice->loadMissing(['party', 'details']);

        return [
            'invoice' => $invoice,
            'isNew' => $isNew,
            'parties' => Party::query()->orderBy('name')->get(),
            'bankAccounts' => BankAccount::query()->orderBy('bank')->orderBy('account')->get(),
            'currencyCodes' => ExchangeRateService::availableCodes(),
        ];
    }

    /** @param array<int, array{description: string, container_number?: ?string, amount: numeric-string|int|float}> $details
     * @return array{subtotal: float, vat_amount: float, total_amount: float}
     */
    private function calculateTotals(array $details, float $vatRate): array
    {
        $subtotal = round(array_sum(array_map(fn (array $detail): float => (float) $detail['amount'], $details)), 2);
        $vatAmount = round($subtotal * $vatRate / 100, 2);

        return [
            'subtotal' => $subtotal,
            'vat_amount' => $vatAmount,
            'total_amount' => round($subtotal + $vatAmount, 2),
        ];
    }
}
