<?php

namespace App\DataTables;

use App\Models\SalesInvoice;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use LogicException;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class SalesInvoicesDataTable extends DataTable
{
    private const STATUS_COLUMN = 'status';

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return DataTables::eloquent($query)
            ->editColumn('invoice_date', fn (SalesInvoice $model) => $model->invoice_date?->format('Y-m-d') ?? '')
            ->editColumn('total_amount', fn (SalesInvoice $model) => $model->currency_code.' '.number_format((float) $model->total_amount, 2))
            ->addColumn('customer', fn (SalesInvoice $model) => $model->party?->name ?? '')
            ->addColumn('bank', fn (SalesInvoice $model) => $model->bankAccount?->bank ?? '')
            ->addColumn('status_badge', fn (SalesInvoice $model) => $this->statusBadge($model))
            ->addColumn('actions', fn (SalesInvoice $model) => $this->actionsHtml($model))
            ->rawColumns(['status_badge', 'actions'])
            ->searchPane(
                $this->statusPaneKey(),
                $this->statusPaneOptions(),
                function (QueryBuilder $query, array $values): void {
                    // ConvertEmptyStringsToNull turns the blank "All" option into null, so test
                    // for a filled value rather than comparing against ''.
                    $values = array_values(array_filter($values, filled(...)));

                    if ($values !== []) {
                        $query->whereIn(self::STATUS_COLUMN, $values);
                    }
                }
            );
    }

    public function query(SalesInvoice $model): QueryBuilder
    {
        return $model->newQuery()->with(['party', 'bankAccount']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('sales-invoices-table')
            ->addTableClass('display min-w-full text-sm')
            ->columns($this->getColumns())
            ->minifiedAjax(
                route('sales-invoices.data'),
                null,
                [],
                ['headers' => ['Accept' => 'application/json, text/javascript, */*; q=0.01']]
            )
            ->orderBy(0, 'desc')
            ->pageLength(25)
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'responsive' => true,
                'autoWidth' => false,
                'pagingType' => 'simple_numbers',
                'dom' => '<"flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6"lf>rt<"flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mt-6"ip>',
                'language' => [
                    'search' => '',
                    'searchPlaceholder' => 'Search sales invoices...',
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
            Column::make('invoice_number')->title('Invoice #')->responsivePriority(1),
            Column::computed('customer')->title('Customer')->orderable(false)->searchable(false)->responsivePriority(2),
            Column::computed('bank')->title('Bank')->orderable(false)->searchable(false)->responsivePriority(7),
            Column::make('invoice_date')->title('Date')->responsivePriority(4),
            Column::make('currency_code')->title('Currency')->responsivePriority(6),
            Column::make('total_amount')->title('Total Amount')->responsivePriority(3),
            Column::make(self::STATUS_COLUMN)
                ->title('Status')
                ->content('status_badge')
                ->searchable(false)
                ->searchPanes(['options' => $this->statusPaneOptions()])
                ->responsivePriority(5),
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

    /**
     * Column index DataTables sends back as the searchPanes key. The panes API
     * keys on position, so this must stay in step with getColumns().
     */
    private function statusPaneKey(): int
    {
        foreach ($this->getColumns() as $index => $column) {
            if ($column->get('data') === self::STATUS_COLUMN) {
                return $index;
            }
        }

        throw new LogicException('The status column must exist in getColumns() for the search pane to work.');
    }

    /** @return array<string, string> */
    private function statusPaneOptions(): array
    {
        return ['' => 'All'] + config('dropdowns.sales_invoices.status', []);
    }

    private function statusBadge(SalesInvoice $model): string
    {
        $labels = config('dropdowns.sales_invoices.status', []);
        $status = $model->status ?: 'unpaid';
        $label = $labels[$status] ?? ucfirst($status);

        if ($status === 'paid') {
            return '<span class="inline-flex items-center rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/10">'.$label.'</span>';
        }

        return '<span class="inline-flex items-center rounded-full bg-amber-50 px-2.5 py-0.5 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/10">'.$label.'</span>';
    }

    private function actionsHtml(SalesInvoice $model): string
    {
        $user = auth()->user();
        $canEdit = $user->isSuperAdmin() || $user->can('sales_invoices.edit');
        $canDelete = $user->isSuperAdmin() || $user->can('sales_invoices.delete');

        $edit = $canEdit
            ? '<a href="'.e(route('sales-invoices.edit', $model)).'" class="group inline-flex h-8 w-8 items-center justify-center rounded-lg text-topbar-muted transition-colors hover:bg-primary-50 hover:text-primary-600" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" /><path d="m15 5 4 4" /></svg></a>'
            : '';

        $pdf = '<a href="'.e(route('sales-invoices.pdf', $model)).'" class="group inline-flex h-8 w-8 items-center justify-center rounded-lg text-topbar-muted transition-colors hover:bg-blue-50 hover:text-blue-600" title="Download PDF"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" /><path d="m7 10 5 5 5-5" /><path d="M12 15V3" /></svg></a>';

        $view = '<a href="'.e(route('sales-invoices.pdf-view', $model)).'" target="_blank" rel="noopener" class="group inline-flex h-8 w-8 items-center justify-center rounded-lg text-topbar-muted transition-colors hover:bg-blue-50 hover:text-blue-600" title="View PDF"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0" /><circle cx="12" cy="12" r="3" /></svg></a>';

        $delete = $canDelete
            ? '<form method="POST" action="'.e(route('sales-invoices.destroy', $model)).'" class="delete-form inline-block" data-confirm="Are you sure you want to delete this sales invoice?"><input type="hidden" name="_token" value="'.csrf_token().'"><input type="hidden" name="_method" value="DELETE"><button type="submit" class="group inline-flex h-8 w-8 items-center justify-center rounded-lg text-topbar-muted transition-colors hover:bg-red-50 hover:text-red-600" title="Delete"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors"><path d="M3 6h18" /><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" /><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" /></svg></button></form>'
            : '';

        return '<div class="flex items-center justify-end gap-2">'.$edit.$view.$pdf.$delete.'</div>';
    }
}
