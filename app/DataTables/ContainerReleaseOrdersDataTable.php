<?php

namespace App\DataTables;

use App\Models\ContainerReleaseOrder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ContainerReleaseOrdersDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return DataTables::eloquent($query)
            ->editColumn('booking_date', fn (ContainerReleaseOrder $model) => $model->booking_date?->format('M j, Y'))
            ->editColumn('cntr_owner', fn (ContainerReleaseOrder $model) => config("dropdowns.container_release_orders.cntr_owner.{$model->cntr_owner}", '—'))
            ->editColumn('commodity_id', fn (ContainerReleaseOrder $model) => $model->commodity?->commodity_number ?? '—')
            ->editColumn('dg_status', fn (ContainerReleaseOrder $model) => config("dropdowns.container_release_orders.dg_status.{$model->dg_status}", '—'))
            ->editColumn('pol_id', fn (ContainerReleaseOrder $model) => $model->pol ? $model->pol->city.', '.$model->pol->country : '—')
            ->editColumn('pofd_id', fn (ContainerReleaseOrder $model) => $model->pofd ? $model->pofd->city.', '.$model->pofd->country : '—')
            ->addColumn('actions', fn (ContainerReleaseOrder $model) => $this->actionsHtml($model))
            ->rawColumns(['actions']);
    }

    public function query(ContainerReleaseOrder $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['commodity', 'pol', 'pofd'])
            ->select(['id', 'booking_id', 'booking_no', 'reference_no', 'booking_date', 'cntr_owner', 'commodity_id', 'dg_status', 'pol_id', 'pofd_id']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('container-release-orders-table')
            ->addTableClass('display min-w-full text-sm')
            ->columns($this->getColumns())
            ->minifiedAjax(ajaxParameters: ['headers' => ['Accept' => 'application/json, text/javascript, */*; q=0.01']])
            ->orderBy(0, 'asc')
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
                    'searchPlaceholder' => 'Search container release orders...',
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
            Column::make('booking_no')->title('Booking #'),
            Column::make('reference_no')->title('Reference #'),
            Column::make('booking_date')->title('Booking Date'),
            Column::make('cntr_owner')->title('Cntr Owner'),
            Column::make('commodity_id')->title('Commodity'),
            Column::make('dg_status')->title('DG Status'),
            Column::make('pol_id')->title('POL'),
            Column::make('pofd_id')->title('POFD'),
            Column::computed('actions')
                ->title('')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('text-right'),
        ];
    }

    private function actionsHtml(ContainerReleaseOrder $model): string
    {
        $user = auth()->user();
        $canView = $user->isSuperAdmin() || $user->can('container_release_orders.view');
        $canEdit = $user->isSuperAdmin() || $user->can('container_release_orders.edit');
        $canDelete = $user->isSuperAdmin() || $user->can('container_release_orders.delete');

        $pdf = $canView
            ? '<a href="'.e(route('container-release-orders.pdf', $model)).'" target="_blank" class="group inline-flex items-center justify-center rounded-lg bg-amber-50 p-2 text-amber-600 transition-all hover:bg-amber-500 hover:shadow-md hover:-translate-y-0.5" title="PDF">'
                .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors group-hover:text-white">'
                .'<path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2" />'
                .'<path d="M6 9V2h12v7" />'
                .'<rect x="6" y="14" width="12" height="8" rx="1" />'
                .'</svg></a>'
            : '';

        $edit = $canEdit
            ? '<a href="'.e(route('container-release-orders.edit', $model)).'" class="group inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 transition-all hover:bg-blue-500 hover:shadow-md hover:-translate-y-0.5" title="Edit">'
                .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors group-hover:text-white">'
                .'<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />'
                .'<path d="m15 5 4 4" />'
                .'</svg></a>'
            : '';

        $delete = $canDelete
            ? '<form method="POST" action="'.e(route('container-release-orders.destroy', $model)).'" class="delete-form inline-block" data-confirm="This will permanently delete this container release order.">'
                .'<input type="hidden" name="_token" value="'.csrf_token().'">'
                .'<input type="hidden" name="_method" value="DELETE">'
                .'<button type="submit" class="group inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:shadow-md hover:-translate-y-0.5" title="Delete">'
                .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors group-hover:text-white">'
                .'<path d="M3 6h18" />'
                .'<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />'
                .'<path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />'
                .'</svg></button></form>'
            : '';

        return '<div class="flex items-center justify-end gap-2">'.$edit.$pdf.$delete.'</div>';
    }
}
