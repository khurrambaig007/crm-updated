<?php

namespace App\Http\Controllers;

use App\DataTables\ContainerPurchaseInvoicesDataTable;
use App\DataTables\ContainerPurchaseModelsDataTable;
use App\DataTables\ContainerPurchaseReleasesDataTable;
use App\DataTables\ContainerPurchasesDataTable;
use App\DataTables\DebiteNotesDataTable;
use App\DataTables\PoCancelsDataTable;
use App\Http\Requests\ContainerPurchase\StoreContainerPurchaseRequest;
use App\Http\Requests\ContainerPurchase\UpdateContainerPurchaseRequest;
use App\Models\Agent;
use App\Models\ContainerKind;
use App\Models\ContainerPurchase;
use App\Models\ContainerPurchaseModel;
use App\Models\ContainerPurchaseRelease;
use App\Models\ContainerSize;
use App\Models\ContainerType;
use App\Models\Currency;
use App\Models\DebiteNote;
use App\Models\Invoice;
use App\Models\PoCancel;
use App\Models\Pol;
use App\Models\SettlementType;
use App\Models\SubCompany;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ContainerPurchaseController extends Controller
{
    public function index()
    {
        return redirect()->route('container-purchases.create');
    }

    public function create(ContainerPurchasesDataTable $dataTable): View
    {
        return view('container-purchases.index', array_merge($this->formData(), [
            'containerPurchase' => null,
            'isNew' => true,
            'nextTransNo' => $this->nextTransNo(),
            'activeTab' => 'details',
            'dataTable' => $dataTable->html(),
        ]));
    }

    public function store(StoreContainerPurchaseRequest $request)
    {
        $data = $request->validated();
        $data['trans_no'] = $data['trans_no'] ?: $this->nextTransNo();

        $containerPurchase = ContainerPurchase::create($data);

        return redirect()->route('container-purchases.edit', $containerPurchase)
            ->with('status', 'Container purchase created successfully.');
    }

    public function edit(ContainerPurchase $containerPurchase, ContainerPurchasesDataTable $dataTable): View
    {
        return view('container-purchases.index', array_merge($this->formData(), [
            'containerPurchase' => $containerPurchase,
            'isNew' => false,
            'nextTransNo' => $containerPurchase->trans_no,
            'activeTab' => 'details',
            'dataTable' => $dataTable->html(),
        ]));
    }

    public function modelsIndex(): View
    {
        return $this->childScreen(
            'container-purchases.partials.purchase',
            'models',
            new ContainerPurchaseModelsDataTable,
            'Purchase',
            'Add and manage container purchase rows.'
        );
    }

    public function invoicesIndex(): View
    {
        return $this->childScreen(
            'container-purchases.partials.invoice',
            'invoices',
            new ContainerPurchaseInvoicesDataTable,
            'Invoice',
            'Add and manage invoice rows.'
        );
    }

    public function releasesIndex(): View
    {
        return $this->childScreen(
            'container-purchases.partials.release',
            'releases',
            new ContainerPurchaseReleasesDataTable,
            'Release',
            'Add and manage release rows.'
        );
    }

    public function debitsIndex(): View
    {
        return $this->childScreen(
            'container-purchases.partials.debit-note',
            'debits',
            new DebiteNotesDataTable,
            'Debit Note',
            'Add and manage debit note rows.'
        );
    }

    public function poCancelsIndex(): View
    {
        return $this->childScreen(
            'container-purchases.partials.po-cancel',
            'po-cancels',
            new PoCancelsDataTable,
            'PO Cancel',
            'Add and manage PO cancel rows.'
        );
    }

    public function update(UpdateContainerPurchaseRequest $request, ContainerPurchase $containerPurchase)
    {
        $containerPurchase->update($request->validated());

        return redirect()->route('container-purchases.edit', $containerPurchase)
            ->with('status', 'Container purchase updated successfully.');
    }

    public function destroy(ContainerPurchase $containerPurchase)
    {
        $containerPurchase->delete();

        return redirect()->route('container-purchases.index')
            ->with('status', 'Container purchase deleted successfully.');
    }

    public function data(ContainerPurchasesDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function modelsData(ContainerPurchaseModelsDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function invoiceData(ContainerPurchaseInvoicesDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function releasesData(ContainerPurchaseReleasesDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function debitData(DebiteNotesDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function poCancelData(PoCancelsDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    public function transactionsForPurchase(Request $request)
    {
        $q = trim((string) $request->query('q')) ?: '';

        if (mb_strlen($q) > 100) {
            return response()->json(['results' => [], 'pagination' => ['more' => false]]);
        }

        if ($q === '') {
            $results = ContainerPurchase::query()
                ->select('id', 'trans_no')
                ->orderByDesc('id')
                ->limit(10)
                ->get()
                ->map(fn (ContainerPurchase $purchase) => ['id' => $purchase->id, 'text' => $purchase->trans_no]);

            return response()->json([
                'results' => $results,
                'pagination' => ['more' => false],
            ]);
        }

        $perPage = 50;
        $page = max(1, (int) $request->query('page', 1));

        $query = ContainerPurchase::query()
            ->select('id', 'trans_no')
            ->where(fn ($query) => $query
                ->where('trans_no', 'like', "{$q}%")
                ->orWhere('trans_no', 'like', "%{$q}"));

        $total = (clone $query)->count();
        $results = $query->orderBy('trans_no')
            ->forPage($page, $perPage)
            ->get()
            ->map(fn (ContainerPurchase $purchase) => ['id' => $purchase->id, 'text' => $purchase->trans_no]);

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => $page * $perPage < $total],
        ]);
    }

    public function storeModel(Request $request): JsonResponse
    {
        ContainerPurchaseModel::create($this->validateChildForm($request, $this->modelRules()));

        return response()->json(['message' => 'Purchase saved successfully.'], 201);
    }

    public function storeInvoice(Request $request): JsonResponse
    {
        Invoice::create($this->withCurrency($this->validateChildForm($request, $this->invoiceRules())));

        return response()->json(['message' => 'Invoice saved successfully.'], 201);
    }

    public function storeRelease(Request $request): JsonResponse
    {
        ContainerPurchaseRelease::create($this->validateChildForm($request, $this->releaseRules()));

        return response()->json(['message' => 'Release saved successfully.'], 201);
    }

    public function storeDebit(Request $request): JsonResponse
    {
        DebiteNote::create($this->withCurrency($this->validateChildForm($request, $this->debitRules())));

        return response()->json(['message' => 'Debit note saved successfully.'], 201);
    }

    public function storePoCancel(Request $request): JsonResponse
    {
        $validated = $this->validateChildForm($request, $this->poCancelRules());
        $validated['doc_no'] = (int) $validated['doc_no'];
        $validated['trans_no'] = (int) $validated['trans_no'];

        PoCancel::create($validated);

        return response()->json(['message' => 'PO cancel saved successfully.'], 201);
    }

    public function updateModel(Request $request, ContainerPurchaseModel $model): JsonResponse
    {
        $model->update($this->validateChildForm($request, $this->modelRules()));

        return response()->json(['message' => 'Purchase updated successfully.']);
    }

    public function updateInvoice(Request $request, Invoice $invoice): JsonResponse
    {
        $invoice->update($this->withCurrency($this->validateChildForm($request, $this->invoiceRules())));

        return response()->json(['message' => 'Invoice updated successfully.']);
    }

    public function updateRelease(Request $request, ContainerPurchaseRelease $release): JsonResponse
    {
        $release->update($this->validateChildForm($request, $this->releaseRules()));

        return response()->json(['message' => 'Release updated successfully.']);
    }

    public function updateDebit(Request $request, DebiteNote $debitNote): JsonResponse
    {
        $debitNote->update($this->withCurrency($this->validateChildForm($request, $this->debitRules())));

        return response()->json(['message' => 'Debit note updated successfully.']);
    }

    public function updatePoCancel(Request $request, PoCancel $poCancel): JsonResponse
    {
        $validated = $this->validateChildForm($request, $this->poCancelRules());
        $validated['doc_no'] = (int) $validated['doc_no'];
        $validated['trans_no'] = (int) $validated['trans_no'];

        $poCancel->update($validated);

        return response()->json(['message' => 'PO cancel updated successfully.']);
    }

    public function destroyModel(ContainerPurchaseModel $model): JsonResponse
    {
        return $this->destroyChild($model, 'Purchase deleted successfully.');
    }

    public function destroyInvoice(Invoice $invoice): JsonResponse
    {
        return $this->destroyChild($invoice, 'Invoice deleted successfully.');
    }

    public function destroyRelease(ContainerPurchaseRelease $release): JsonResponse
    {
        return $this->destroyChild($release, 'Release deleted successfully.');
    }

    public function destroyDebit(DebiteNote $debitNote): JsonResponse
    {
        return $this->destroyChild($debitNote, 'Debit note deleted successfully.');
    }

    public function destroyPoCancel(PoCancel $poCancel): JsonResponse
    {
        return $this->destroyChild($poCancel, 'PO cancel deleted successfully.');
    }

    private function destroyChild(Model $model, string $message): JsonResponse
    {
        try {
            $model->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'Cannot delete this record because it is referenced by other records.',
            ], 409);
        }

        return response()->json(['message' => $message]);
    }

    private function modelRules(): array
    {
        return [
            'container_purchase_detail_id' => ['nullable', 'integer', 'exists:container_purchases,id'],
            'container_size_id' => ['required', 'integer', 'exists:container_sizes,id'],
            'container_type_id' => ['required', 'integer', 'exists:container_types,id'],
            'container_kind_id' => ['required', 'integer', 'exists:container_kinds,id'],
            'quantity' => ['nullable', 'numeric', 'min:0'],
            'amount' => ['nullable', 'numeric'],
            'rate' => ['nullable', 'numeric'],
        ];
    }

    private function invoiceRules(): array
    {
        return [
            'doc_no' => ['required', 'string', 'max:191'],
            'invoice_no' => ['required', 'string', 'max:191'],
            'invoice_date' => ['required', 'date'],
            'settlement_type_id' => ['required', 'integer', 'exists:settlement_types,id'],
            'payment_agent_id' => ['required', 'integer', 'exists:agents,id'],
            'amount' => ['required', 'numeric'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'location_id' => ['required', 'integer', 'exists:pols,id'],
            'sub_company_id' => ['required', 'integer', 'exists:sub_companies,id'],
            'currency' => ['required', 'string', 'max:10'],
            'currency_code' => ['nullable', 'string', 'max:10'],
            'currency_exchange_rate' => ['nullable', 'string', 'max:191'],
            'total_amount' => ['nullable', 'numeric'],
            'container_purchase_detail_id' => ['nullable', 'integer', 'exists:container_purchases,id'],
        ];
    }

    private function releaseRules(): array
    {
        return [
            'container_purchase_detail_id' => ['nullable', 'integer', 'exists:container_purchases,id'],
            'container_number' => ['nullable', 'string', 'max:191'],
            'container_size' => ['nullable', 'integer', 'exists:container_sizes,id'],
            'container_type' => ['nullable', 'integer', 'exists:container_types,id'],
            'container_kind' => ['nullable', 'integer', 'exists:container_kinds,id'],
            'm_f_year' => ['nullable', 'string', 'max:191'],
            'rate' => ['nullable', 'numeric'],
            'remarks' => ['nullable', 'string', 'max:500'],
            'original_container_number' => ['nullable', 'string', 'max:500'],
        ];
    }

    private function debitRules(): array
    {
        return [
            'doc_no' => ['required', 'string', 'max:191'],
            'invoice_id' => ['required', 'integer', 'exists:invoices,id'],
            'settlement_type_id' => ['required', 'integer', 'exists:settlement_types,id'],
            'payment_agent_id' => ['required', 'integer', 'exists:agents,id'],
            'amount' => ['required', 'numeric'],
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'location_id' => ['required', 'integer', 'exists:pols,id'],
            'sub_company_id' => ['required', 'integer', 'exists:sub_companies,id'],
            'currency' => ['required', 'string', 'max:10'],
            'currency_code' => ['nullable', 'string', 'max:10'],
            'currency_exchange_rate' => ['nullable', 'string', 'max:191'],
            'total_amount' => ['nullable', 'numeric'],
            'container_purchase_detail_id' => ['nullable', 'integer', 'exists:container_purchases,id'],
        ];
    }

    private function poCancelRules(): array
    {
        return [
            'doc_no' => ['required', 'integer'],
            'trans_no' => ['required', 'integer'],
            'transaction_date' => ['required', 'date'],
        ];
    }

    private function withCurrency(array $validated): array
    {
        $validated['currency_id'] = $this->resolveCurrencyId((string) $validated['currency']);
        unset($validated['currency']);

        return $validated;
    }

    /**
     * Validate a child-tab form input. These endpoints are always called via
     * AJAX, so render failures as JSON even though the app only defaults to
     * JSON exceptions on api/* routes.
     */
    private function validateChildForm(Request $request, array $rules): array
    {
        try {
            return Validator::make($request->all(), $rules)->validate();
        } catch (ValidationException $e) {
            throw new HttpResponseException(
                response()->json(['message' => $e->getMessage(), 'errors' => $e->errors()], 422)
            );
        }
    }

    private function resolveCurrencyId(?string $code): ?int
    {
        if ($code === null || $code === '') {
            return null;
        }

        $latest = Currency::orderBy('exchange_rate_date', 'desc')->first();

        $rates = $latest ? json_decode($latest->exchange_rate ?? '', true) : [];
        if (is_array($rates) && array_key_exists($code, $rates)) {
            return $latest->id;
        }

        $match = Currency::get()->first(function (Currency $currency) use ($code): bool {
            $rates = json_decode($currency->exchange_rate ?? '', true);

            return is_array($rates) && array_key_exists($code, $rates);
        });

        return $match?->id ?? $latest?->id;
    }

    private function childScreen(string $partial, string $activeTab, object $childTable, string $title, string $description): View
    {
        return view('container-purchases.child', array_merge($this->formData(), [
            'containerPurchase' => null,
            'isNew' => true,
            'childTitle' => $title,
            'childDescription' => $description,
            'formPartial' => $partial,
            'activeTab' => $activeTab,
            'childTable' => $childTable->html(),
        ]));
    }

    private function formData(): array
    {
        $currency = Currency::orderBy('exchange_rate_date', 'desc')->first();
        $rates = $currency ? (json_decode($currency->exchange_rate ?? '', true) ?: []) : [];
        $currencies = ! empty($rates)
            ? array_combine(array_keys($rates), array_keys($rates))
            : config('dropdowns.bookings.detention_currency');

        return [
            'suppliers' => Supplier::orderBy('name')->pluck('name', 'id'),
            'ports' => Pol::orderBy('city')->get()->mapWithKeys(
                fn (Pol $p) => [$p->id => '('.$p->port_code.') '.$p->city.', '.$p->country]
            ),
            'agents' => Agent::orderBy('name')->get()->mapWithKeys(
                fn (Agent $a) => [$a->id => '('.$a->code.') '.$a->name]
            ),
            'currencies' => $currencies,
            'rates' => $rates,
            'exchangeRateDate' => $currency?->exchange_rate_date?->format('Y-m-d'),
            'purchaseTypes' => config('dropdowns.container_purchases.purchase_type'),
            'principals' => config('dropdowns.container_purchases.principal'),
            'containerSizes' => ContainerSize::orderBy('size')->pluck('size', 'id'),
            'containerTypes' => ContainerType::orderBy('name')->pluck('name', 'id'),
            'containerKinds' => ContainerKind::orderBy('name')->pluck('name', 'id'),
            'settlementTypes' => SettlementType::orderBy('name')->pluck('name', 'id'),
            'subCompanies' => SubCompany::orderBy('name')->pluck('name', 'id'),
            'invoices' => Invoice::orderByDesc('id')->pluck('invoice_no', 'id'),
        ];
    }

    private function nextTransNo(): string
    {
        $max = ContainerPurchase::selectRaw('MAX(CAST(SUBSTRING(trans_no, 4) AS UNSIGNED)) as m')->value('m');
        $max = $max ?: 0;

        return 'VRM'.str_pad((string) ($max + 1), 10, '0', STR_PAD_LEFT);
    }
}
