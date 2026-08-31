<?php

namespace App\DataTables;

use App\Models\ContainerPurchaseInvoice;
use App\Models\Currency;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ContainerPurchaseInvoicesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return DataTables::eloquent($query)
            ->editColumn('invoice_date', fn (ContainerPurchaseInvoice $model) => $model->invoice_date?->format('Y-m-d') ?? '')
            ->addColumn('settlement_type', fn (ContainerPurchaseInvoice $model) => $model->settlementType?->name ?? '')
            ->addColumn('payment_agent', fn (ContainerPurchaseInvoice $model) => $model->paymentAgent?->name ?? '')
            ->addColumn('currency', function (ContainerPurchaseInvoice $model) {
                $code = $model->currency_code;
                if (! $code) {
                    return '';
                }
                $latest = Currency::orderBy('exchange_rate_date', 'desc')->first();
                $rates = $latest ? (json_decode($latest->exchange_rate ?? '', true) ?: []) : [];
                $rate = $rates[$code] ?? null;

                return $rate !== null ? $code.' (@'.$rate.')' : $code;
            })
            ->addColumn('supplier', fn (ContainerPurchaseInvoice $model) => $model->supplier?->name ?? '')
            ->addColumn('location', fn (ContainerPurchaseInvoice $model) => $model->location?->city ?? '')
            ->addColumn('sub_company', fn (ContainerPurchaseInvoice $model) => $model->subCompany?->name ?? '')
            ->addColumn('actions', fn (ContainerPurchaseInvoice $model) => $this->actionsHtml($model))
            ->rawColumns(['actions']);
    }

    public function query(ContainerPurchaseInvoice $model): QueryBuilder
    {
        return $model->newQuery()->with(['settlementType', 'paymentAgent', 'currency', 'supplier', 'location', 'subCompany', 'containerPurchase']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('cp-invoice-table')
            ->addTableClass('display min-w-full text-sm')
            ->columns($this->getColumns())
            ->minifiedAjax(
                route('container-purchases.invoice-data'),
                null,
                [],
                ['headers' => ['Accept' => 'application/json, text/javascript, */*; q=0.01']]
            )
            ->orderBy(1, 'desc')
            ->pageLength(10)
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'autoWidth' => false,
                'pagingType' => 'simple_numbers',
                'dom' => '<"flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6"lf>rt<"flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mt-6"ip>',
                'language' => [
                    'search' => '',
                    'searchPlaceholder' => 'Search...',
                    'lengthMenu' => '_MENU_ <span class="text-sm text-gray-500">entries per page</span>',
                    'info' => 'Showing <span class="font-semibold text-gray-700">_START_</span> to <span class="font-semibold text-gray-700">_END_</span> of <span class="font-semibold text-gray-700">_TOTAL_</span> entries',
                    'paginate' => [
                        'previous' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m15 18-6-6 6-6"/></svg>',
                        'next' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m9 18 6-6-6-6"/></svg>',
                    ],
                ],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('id')->title('ID')->responsivePriority(1),
            Column::make('doc_no')->title('Doc No.')->orderable(false)->searchable(false)->responsivePriority(2),
            Column::make('invoice_no')->title('Invoice No.')->orderable(false)->searchable(false)->responsivePriority(3),
            Column::make('invoice_date')->title('Invoice Date')->responsivePriority(4),
            Column::computed('settlement_type')->title('Settlement Type')->orderable(false)->searchable(false)->responsivePriority(5),
            Column::computed('payment_agent')->title('Payment Agent')->orderable(false)->searchable(false)->responsivePriority(6),
            Column::computed('currency')->title('Currency')->orderable(false)->searchable(false)->responsivePriority(7),
            Column::make('amount')->title('Amount')->orderable(false)->searchable(false)->responsivePriority(8),
            Column::make('total_amount')->title('Total Amount')->orderable(false)->searchable(false)->responsivePriority(8),
            Column::computed('supplier')->title('Supplier')->orderable(false)->searchable(false)->responsivePriority(9),
            Column::computed('location')->title('Location')->orderable(false)->searchable(false)->responsivePriority(10),
            Column::computed('sub_company')->title('Sub Company')->orderable(false)->searchable(false)->responsivePriority(11),
            Column::computed('actions')
                ->title('')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->responsivePriority(1)
                ->addClass('text-right'),
        ];
    }

    public function ajax(): JsonResponse
    {
        return $this->dataTable($this->query(new ContainerPurchaseInvoice))->toJson();
    }

    private function actionsHtml(ContainerPurchaseInvoice $model): string
    {
        $user = auth()->user();
        $canEdit = $user->isSuperAdmin() || $user->can('container_purchases.edit');
        $canDelete = $user->isSuperAdmin() || $user->can('container_purchases.delete');

        $payload = [
            'doc_no' => $model->doc_no,
            'invoice_no' => $model->invoice_no,
            'invoice_date' => $model->invoice_date?->format('Y-m-d'),
            'settlement_type_id' => $model->settlement_type_id,
            'payment_agent_id' => $model->payment_agent_id,
            'currency' => $model->currency_code,
            'amount' => $model->amount,
            'supplier_id' => $model->supplier_id,
            'location_id' => $model->location_id,
            'sub_company_id' => $model->sub_company_id,
            'currency_exchange_rate' => $model->currency_exchange_rate,
            'currency_code' => $model->currency_code,
            'total_amount' => $model->total_amount,
            'container_purchase_detail_id' => $model->container_purchase_detail_id,
            'container_purchase_trans_no' => $model->containerPurchase?->trans_no,
        ];

        $edit = $canEdit
            ? '<button type="button" class="cp-row-edit inline-flex h-8 w-8 items-center justify-center rounded-lg text-topbar-muted transition-colors hover:bg-primary-50 hover:text-primary-600" data-edit="'.e(json_encode($payload)).'" data-update-url="'.e(route('container-purchases.invoices.update', $model)).'" data-table-id="cp-invoice-table" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg></button>'
            : '';

        $delete = $canDelete
            ? '<button type="button" class="cp-row-delete inline-flex h-8 w-8 items-center justify-center rounded-lg text-topbar-muted transition-colors hover:bg-red-500 hover:text-white" data-delete-url="'.e(route('container-purchases.invoices.destroy', $model)).'" data-confirm="Are you sure you want to delete this invoice?" data-table-id="cp-invoice-table" title="Delete"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>'
            : '';

        return '<div class="flex items-center justify-end gap-2">'.$edit.$delete.'</div>';
    }
}
