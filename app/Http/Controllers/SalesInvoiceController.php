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
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesInvoiceController extends Controller
{
    public function index(SalesInvoicesDataTable $dataTable): mixed
    {
        if (request()->ajax()) {
            return $dataTable->ajax();
        }

        return $dataTable->render('sales-invoices.index');
    }

    public function getData(SalesInvoicesDataTable $dataTable): mixed
    {
        return $dataTable->ajax();
    }

    public function create(): View
    {
        return view('sales-invoices.edit', $this->formData(new SalesInvoice, true));
    }

    public function store(StoreSalesInvoiceRequest $request): RedirectResponse
    {
        $invoice = DB::transaction(function () use ($request): SalesInvoice {
            $validated = $request->validated();
            $details = $validated['details'];
            unset($validated['details']);

            $totals = $this->calculateTotals($details, (float) $validated['vat_rate']);
            $invoice = SalesInvoice::create(array_merge($validated, $totals, ['invoice_number' => null]));
            $invoice->update(['invoice_number' => 'APX'.str_pad((string) $invoice->id, 8, '0', STR_PAD_LEFT)]);
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

    public function navigate(SalesInvoice $salesInvoice): JsonResponse
    {
        $salesInvoice->load(['party', 'details']);

        return response()->json(array_merge([
            'invoice' => $salesInvoice,
        ], $this->navigationData($salesInvoice)));
    }

    public function downloadPdf(SalesInvoice $salesInvoice): Response
    {
        $salesInvoice->load(['party', 'details']);
        $profile = CompanyProfile::current();
        $bankAccount = BankAccount::current();

        return Pdf::loadView('sales-invoices.pdf', [
            'invoice' => $salesInvoice,
            'company' => $profile,
            'bankAccount' => $bankAccount,
            'brandLogoPath' => $profile->logoPath(),
            'brandLogoSize' => $profile->logoDisplaySize(240, 125),
        ])->setPaper('a4', 'portrait')->download($salesInvoice->invoice_number.'.pdf');
    }

    /** @return array<string, mixed> */
    private function formData(SalesInvoice $invoice, bool $isNew): array
    {
        $invoice->loadMissing(['party', 'details']);

        return array_merge([
            'invoice' => $invoice,
            'isNew' => $isNew,
            'parties' => Party::query()->orderBy('name')->get(),
            'currencyCodes' => ExchangeRateService::availableCodes(),
        ], $this->navigationData($invoice));
    }

    /** @return array<string, int|null> */
    private function navigationData(SalesInvoice $invoice): array
    {
        $query = SalesInvoice::query();
        $total = (clone $query)->count();

        return [
            'total' => $total,
            'current' => $invoice->exists ? (clone $query)->where('id', '<=', $invoice->id)->count() : $total + 1,
            'firstId' => (clone $query)->orderBy('id')->value('id'),
            'lastId' => (clone $query)->orderByDesc('id')->value('id'),
            'prevId' => $invoice->exists ? (clone $query)->where('id', '<', $invoice->id)->orderByDesc('id')->value('id') : null,
            'nextId' => $invoice->exists ? (clone $query)->where('id', '>', $invoice->id)->orderBy('id')->value('id') : null,
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
