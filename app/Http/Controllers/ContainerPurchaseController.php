<?php

namespace App\Http\Controllers;

use App\DataTables\ContainerPurchasesDataTable;
use App\DataTables\PlaceholderDataTable;
use App\Http\Requests\ContainerPurchase\StoreContainerPurchaseRequest;
use App\Http\Requests\ContainerPurchase\UpdateContainerPurchaseRequest;
use App\Models\Agent;
use App\Models\ContainerPurchase;
use App\Models\Currency;
use App\Models\Pol;
use App\Models\Supplier;
use Illuminate\View\View;

class ContainerPurchaseController extends Controller
{
    public function index()
    {
        $last = ContainerPurchase::orderByDesc('id')->first();

        return $last
            ? redirect()->route('container-purchases.edit', $last)
            : redirect()->route('container-purchases.create');
    }

    public function create(ContainerPurchasesDataTable $dataTable): View
    {
        return view('container-purchases.index', array_merge($this->formData(), $this->placeholderTables(), [
            'containerPurchase' => null,
            'isNew' => true,
            'nextTransNo' => $this->nextTransNo(),
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
        return view('container-purchases.index', array_merge($this->formData(), $this->placeholderTables(), [
            'containerPurchase' => $containerPurchase,
            'isNew' => false,
            'nextTransNo' => $containerPurchase->trans_no,
            'dataTable' => $dataTable->html(),
        ]));
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

    public function placeholderData(PlaceholderDataTable $dataTable)
    {
        return $dataTable->ajax();
    }

    private function placeholderTables(): array
    {
        $make = fn (string $id) => (new PlaceholderDataTable)->setTableId($id)->html();

        return [
            'purchaseTable' => $make('cp-purchase-table'),
            'invoiceTable' => $make('cp-invoice-table'),
            'releaseTable' => $make('cp-release-table'),
            'debitTable' => $make('cp-debit-table'),
            'poCancelTable' => $make('cp-po-cancel-table'),
        ];
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
        ];
    }

    private function nextTransNo(): string
    {
        $max = ContainerPurchase::selectRaw('MAX(CAST(SUBSTRING(trans_no, 4) AS UNSIGNED)) as m')->value('m');
        $max = $max ?: 0;

        return 'VRM'.str_pad((string) ($max + 1), 10, '0', STR_PAD_LEFT);
    }
}
