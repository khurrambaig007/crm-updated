<?php

namespace App\DataTables;

use App\Models\ContainerKind;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ContainerKindsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return DataTables::eloquent($query)
            ->editColumn('created_at', fn (ContainerKind $model) => $model->created_at?->format('M j, Y'))
            ->addColumn('actions', fn (ContainerKind $model) => $this->actionsHtml($model))
            ->rawColumns(['actions']);
    }

    public function query(ContainerKind $model): QueryBuilder
    {
        return $model->newQuery()->select(['id', 'name', 'created_at']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('container-kinds-table')
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
                    'searchPlaceholder' => 'Search container kinds...',
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
            Column::make('name')->title('Name'),
            Column::make('created_at')->title('Created'),
            Column::computed('actions')
                ->title('')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('text-right'),
        ];
    }

    private function actionsHtml(ContainerKind $model): string
    {
        $user = auth()->user();
        $canEdit = $user->isSuperAdmin() || $user->can('container_kinds.edit');
        $canDelete = $user->isSuperAdmin() || $user->can('container_kinds.delete');

        $edit = $canEdit
            ? '<a href="'.e(route('container-kinds.edit', $model)).'" class="group inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 transition-all hover:bg-blue-500 hover:shadow-md hover:-translate-y-0.5" title="Edit">'
                .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors group-hover:text-white">'
                .'<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />'
                .'<path d="m15 5 4 4" />'
                .'</svg></a>'
            : '';

        $delete = $canDelete
            ? '<form method="POST" action="'.e(route('container-kinds.destroy', $model)).'" class="delete-form inline-block" data-confirm="This will permanently delete this container kind.">'
                .'<input type="hidden" name="_token" value="'.csrf_token().'">'
                .'<input type="hidden" name="_method" value="DELETE">'
                .'<button type="submit" class="group inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:shadow-md hover:-translate-y-0.5" title="Delete">'
                .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors group-hover:text-white">'
                .'<path d="M3 6h18" />'
                .'<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />'
                .'<path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />'
                .'</svg></button></form>'
            : '';

        return '<div class="flex items-center justify-end gap-2">'.$edit.$delete.'</div>';
    }
}
