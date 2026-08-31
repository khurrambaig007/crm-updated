<?php

namespace App\DataTables;

use App\Models\ContainerPurchaseRelease;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ContainerPurchaseReleasesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return DataTables::eloquent($query)
            ->addColumn('trans_no', fn (ContainerPurchaseRelease $model) => $model->containerPurchase?->trans_no ?? '')
            ->addColumn('container_size', fn (ContainerPurchaseRelease $model) => $model->containerSize?->size ?? '')
            ->addColumn('container_type', fn (ContainerPurchaseRelease $model) => $model->containerType?->name ?? '')
            ->addColumn('container_kind', fn (ContainerPurchaseRelease $model) => $model->containerKind?->name ?? '')
            ->addColumn('actions', fn (ContainerPurchaseRelease $model) => $this->actionsHtml($model))
            ->rawColumns(['actions']);
    }

    public function query(ContainerPurchaseRelease $model): QueryBuilder
    {
        return $model->newQuery()->with(['containerSize', 'containerType', 'containerKind', 'containerPurchase']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('cp-release-table')
            ->addTableClass('display min-w-full text-sm')
            ->columns($this->getColumns())
            ->minifiedAjax(
                route('container-purchases.releases-data'),
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
            Column::computed('trans_no')->title('Transaction No.')->orderable(false)->searchable(false)->responsivePriority(1),
            Column::make('container_number')->title('Container No.')->orderable(false)->searchable(false)->responsivePriority(2),
            Column::computed('container_size')->title('Size')->orderable(false)->searchable(false)->responsivePriority(3),
            Column::computed('container_type')->title('Type')->orderable(false)->searchable(false)->responsivePriority(4),
            Column::computed('container_kind')->title('Kind')->orderable(false)->searchable(false)->responsivePriority(5),
            Column::make('m_f_year')->title('M/F Year')->orderable(false)->searchable(false)->responsivePriority(6),
            Column::make('rate')->title('Rate')->orderable(false)->searchable(false)->responsivePriority(7),
            Column::make('original_container_number')->title('Original Container No.')->orderable(false)->searchable(false)->responsivePriority(8),
            Column::make('remarks')->title('Remarks')->orderable(false)->searchable(false)->responsivePriority(9),
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
        return $this->dataTable($this->query(new ContainerPurchaseRelease))->toJson();
    }

    private function actionsHtml(ContainerPurchaseRelease $model): string
    {
        $user = auth()->user();
        $canEdit = $user->isSuperAdmin() || $user->can('container_purchases.edit');
        $canDelete = $user->isSuperAdmin() || $user->can('container_purchases.delete');

        $payload = [
            'container_purchase_detail_id' => $model->container_purchase_detail_id,
            'container_number' => $model->container_number,
            'container_size' => $model->container_size,
            'container_type' => $model->container_type,
            'container_kind' => $model->container_kind,
            'm_f_year' => $model->m_f_year,
            'rate' => $model->rate,
            'remarks' => $model->remarks,
            'original_container_number' => $model->original_container_number,
            'container_purchase_trans_no' => $model->containerPurchase?->trans_no,
        ];

        $edit = $canEdit
            ? '<button type="button" class="cp-row-edit inline-flex h-8 w-8 items-center justify-center rounded-lg text-topbar-muted transition-colors hover:bg-primary-50 hover:text-primary-600" data-edit="'.e(json_encode($payload)).'" data-update-url="'.e(route('container-purchases.releases.update', $model)).'" data-table-id="cp-release-table" title="Edit"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/><path d="m15 5 4 4"/></svg></button>'
            : '';

        $delete = $canDelete
            ? '<button type="button" class="cp-row-delete inline-flex h-8 w-8 items-center justify-center rounded-lg text-topbar-muted transition-colors hover:bg-red-500 hover:text-white" data-delete-url="'.e(route('container-purchases.releases.destroy', $model)).'" data-confirm="Are you sure you want to delete this release?" data-table-id="cp-release-table" title="Delete"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg></button>'
            : '';

        return '<div class="flex items-center justify-end gap-2">'.$edit.$delete.'</div>';
    }
}
