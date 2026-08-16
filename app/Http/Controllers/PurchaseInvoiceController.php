<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseInvoice\StorePurchaseInvoiceRequest;
use App\Http\Requests\PurchaseInvoice\UpdatePurchaseInvoiceRequest;
use App\Models\Charge;
use App\Models\ContainerSize;
use App\Models\ContainerType;
use App\Models\Currency;
use App\Models\Pol;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetail;
use App\Models\SettlementType;
use App\Models\SubCompany;
use App\Models\Supplier;
use App\Services\ExchangeRateService;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PurchaseInvoiceController extends Controller
{
    public function index(): RedirectResponse
    {
        $last = PurchaseInvoice::orderBy('id', 'desc')->first();

        if ($last) {
            return redirect()->route('purchase-invoices.edit', $last);
        }

        return redirect()->route('purchase-invoices.create');
    }

    public function create(): View
    {
        $invoice = new PurchaseInvoice;
        $total = PurchaseInvoice::count();
        $current = $total + 1;
        $firstId = PurchaseInvoice::orderBy('id')->value('id');
        $lastId = PurchaseInvoice::orderBy('id', 'desc')->value('id');

        return view('purchase-invoices.edit', array_merge($this->formData(), [
            'invoice' => $invoice,
            'total' => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId' => $lastId,
            'prevId' => null,
            'nextId' => null,
        ]));
    }

    public function store(StorePurchaseInvoiceRequest $request): RedirectResponse
    {
        $invoice = PurchaseInvoice::create($request->validated());

        return redirect()->route('purchase-invoices.edit', $invoice)->with('status', 'Purchase invoice created successfully.');
    }

    public function edit(PurchaseInvoice $purchaseInvoice): View
    {
        $invoice = $purchaseInvoice->load('details');
        $total = PurchaseInvoice::count();
        $current = PurchaseInvoice::where('id', '<=', $invoice->id)->count();
        $firstId = PurchaseInvoice::orderBy('id')->value('id');
        $lastId = PurchaseInvoice::orderBy('id', 'desc')->value('id');
        $prevId = PurchaseInvoice::where('id', '<', $invoice->id)->orderBy('id', 'desc')->value('id');
        $nextId = PurchaseInvoice::where('id', '>', $invoice->id)->orderBy('id')->value('id');

        return view('purchase-invoices.edit', array_merge($this->formData(), [
            'invoice' => $invoice,
            'total' => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId' => $lastId,
            'prevId' => $prevId,
            'nextId' => $nextId,
        ]));
    }

    public function update(UpdatePurchaseInvoiceRequest $request, PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        $purchaseInvoice->update($request->validated());

        return redirect()->route('purchase-invoices.edit', $purchaseInvoice)->with('status', 'Purchase invoice updated successfully.');
    }

    public function destroy(Request $request, PurchaseInvoice $purchaseInvoice): RedirectResponse
    {
        try {
            $purchaseInvoice->details()->delete();
            $purchaseInvoice->delete();
        } catch (QueryException $e) {
            return redirect()->route('purchase-invoices.index')->with('error', 'This invoice is in use and cannot be deleted.');
        }

        return redirect()->route('purchase-invoices.index')->with('status', 'Purchase invoice deleted successfully.');
    }

    public function navigate(Request $request, PurchaseInvoice $purchaseInvoice): JsonResponse
    {
        $invoice = $purchaseInvoice->load('details');
        $total = PurchaseInvoice::count();
        $current = PurchaseInvoice::where('id', '<=', $invoice->id)->count();
        $firstId = PurchaseInvoice::orderBy('id')->value('id');
        $lastId = PurchaseInvoice::orderBy('id', 'desc')->value('id');
        $prevId = PurchaseInvoice::where('id', '<', $invoice->id)->orderBy('id', 'desc')->value('id');
        $nextId = PurchaseInvoice::where('id', '>', $invoice->id)->orderBy('id')->value('id');

        return response()->json([
            'invoice' => $invoice,
            'total' => $total,
            'current' => $current,
            'firstId' => $firstId,
            'lastId' => $lastId,
            'prevId' => $prevId,
            'nextId' => $nextId,
        ]);
    }

    public function storeDetail(Request $request, PurchaseInvoice $purchaseInvoice): JsonResponse
    {
        $validated = $request->validate([
            'charges' => ['nullable', 'string', 'max:191'],
            'type' => ['nullable', 'string', 'max:191'],
            'm_r_number' => ['nullable', 'string', 'max:191'],
            'bl_number' => ['nullable', 'string', 'max:191'],
            'container_number' => ['nullable', 'string', 'max:191'],
            'size' => ['nullable', 'string', 'max:191'],
            'size_type' => ['nullable', 'string', 'max:191'],
            'amount' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'max:191'],
            'exchange_rate' => ['nullable', 'string', 'max:191'],
            'amount_dollar' => ['nullable', 'numeric'],
            'vat_percentage' => ['nullable', 'string', 'max:191'],
            'vat_amount' => ['nullable', 'numeric'],
            'vat_amount_dollar' => ['nullable', 'numeric'],
            'remarks' => ['nullable', 'string', 'max:191'],
        ]);

        $detail = $purchaseInvoice->details()->create($validated);
        $this->recalculateTotals($purchaseInvoice);

        return response()->json(['detail' => $detail, 'message' => 'Detail saved.']);
    }

    public function updateDetail(Request $request, PurchaseInvoice $purchaseInvoice, PurchaseInvoiceDetail $detail): JsonResponse
    {
        $validated = $request->validate([
            'charges' => ['nullable', 'string', 'max:191'],
            'type' => ['nullable', 'string', 'max:191'],
            'm_r_number' => ['nullable', 'string', 'max:191'],
            'bl_number' => ['nullable', 'string', 'max:191'],
            'container_number' => ['nullable', 'string', 'max:191'],
            'size' => ['nullable', 'string', 'max:191'],
            'size_type' => ['nullable', 'string', 'max:191'],
            'amount' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'max:191'],
            'exchange_rate' => ['nullable', 'string', 'max:191'],
            'amount_dollar' => ['nullable', 'numeric'],
            'vat_percentage' => ['nullable', 'string', 'max:191'],
            'vat_amount' => ['nullable', 'numeric'],
            'vat_amount_dollar' => ['nullable', 'numeric'],
            'remarks' => ['nullable', 'string', 'max:191'],
        ]);

        $detail->update($validated);
        $this->recalculateTotals($purchaseInvoice);

        return response()->json(['detail' => $detail, 'message' => 'Detail updated.']);
    }

    public function destroyDetail(Request $request, PurchaseInvoice $purchaseInvoice, PurchaseInvoiceDetail $detail): JsonResponse
    {
        $detail->delete();
        $this->recalculateTotals($purchaseInvoice);

        return response()->json(['message' => 'Detail deleted.']);
    }

    private function recalculateTotals(PurchaseInvoice $invoice): void
    {
        $details = $invoice->details()->get();

        $total = $details->sum('amount');
        $vat = $details->sum('vat_amount');
        $netAmount = $total + $vat;

        $invoice->updateQuietly([
            'total' => $total,
            'vat' => $vat,
            'net_amount' => $netAmount,
        ]);
    }

    private function formData(): array
    {
        $currency = Currency::orderBy('exchange_rate_date', 'desc')->first();
        $rates = [];

        if ($currency) {
            $rates = json_decode($currency->exchange_rate ?? '', true) ?: [];
        }

        return [
            'vendors' => Supplier::orderBy('name')->get(),
            'ports' => Pol::orderBy('city')->get(),
            'settlementTypes' => SettlementType::orderBy('name')->get(),
            'subCompanies' => SubCompany::orderBy('name')->get(),
            'charges' => Charge::orderBy('name')->get(),
            'containerTypes' => ContainerType::orderBy('name')->get(),
            'containerSizes' => ContainerSize::orderBy('size')->get(),
            'rates' => $rates,
            'currencyCodes' => ExchangeRateService::CURRENCIES,
        ];
    }
}
