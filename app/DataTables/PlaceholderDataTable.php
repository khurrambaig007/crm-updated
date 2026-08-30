<?php

namespace App\DataTables;

use App\Models\ContainerPurchase;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PlaceholderDataTable extends DataTable
{
    protected string $tableId = 'placeholder-table';

    public function setTableId(string $tableId): self
    {
        $this->tableId = $tableId;

        return $this;
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return DataTables::eloquent($query)
            ->addColumn('actions', static fn () => '');
    }

    public function query(ContainerPurchase $model): QueryBuilder
    {
        // No real data source wired yet — return an empty result set.
        return $model->newQuery()->whereRaw('1 = 0');
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId($this->tableId)
            ->addTableClass('display min-w-full text-sm')
            ->columns($this->getColumns())
            ->minifiedAjax(
                route('container-purchases.placeholder-data'),
                null,
                [],
                ['headers' => ['Accept' => 'application/json, text/javascript, */*; q=0.01']]
            )
            ->orderBy(1)
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
            Column::make('id')->title('ID'),
            Column::make('reference')->title('Reference')->orderable(false)->searchable(false),
            Column::make('date')->title('Date')->orderable(false)->searchable(false),
            Column::make('status')->title('Status')->orderable(false)->searchable(false),
            Column::computed('actions')
                ->title('')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('text-right'),
        ];
    }

    public function ajax()
    {
        return $this->dataTable($this->query(new ContainerPurchase))->toJson();
    }
}
