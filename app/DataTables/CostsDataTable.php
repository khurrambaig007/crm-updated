<?php

namespace App\DataTables;

use App\Models\Cost;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CostsDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return DataTables::eloquent($query)
            ->editColumn('pol_id', fn (Cost $model) => $model->pol?->city ?? '—')
            ->editColumn('pod_id', fn (Cost $model) => $model->pod?->city ?? '—')
            ->editColumn('container_type_id', fn (Cost $model) => $model->containerType?->name ?? '—')
            ->editColumn('feeder_id', fn (Cost $model) => $model->feeder?->name ?? '—')
            ->editColumn('container_size_id', fn (Cost $model) => $model->containerSize?->size ?? '—')
            ->editColumn('slot_term_id', fn (Cost $model) => $model->slotTerm?->term ?? '—')
            ->editColumn('pod_agent_id', fn (Cost $model) => $model->podAgent?->name ?? '—')
            ->editColumn('pol_agent_id', fn (Cost $model) => $model->polAgent?->name ?? '—')
            ->editColumn('created_at', fn (Cost $model) => $model->created_at?->format('M j, Y'))
            ->addColumn('actions', fn (Cost $model) => $this->actionsHtml($model))
            ->rawColumns(['actions']);
    }

    public function query(Cost $model): QueryBuilder
    {
        return $model->newQuery()
            ->with(['pol', 'pod', 'containerType', 'feeder', 'containerSize', 'slotTerm', 'podAgent', 'polAgent'])
            ->select(['id', 'pol_id', 'pod_id', 'container_type_id', 'feeder_id', 'container_size_id', 'slot_term_id', 'pod_agent_id', 'pol_agent_id', 'slot', 'dthc', 'wrr', 'ts_thc', 'ts_commission', 'of', 'pod_rebate', 'free_days', 'total_cost', 'total_collection', 'net_shipping', 'created_at']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('costs-table')
            ->addTableClass('display text-sm w-full border-collapse')
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
                    'searchPlaceholder' => 'Search costs...',
                    'lengthMenu' => '_MENU_ <span class="text-sm text-gray-500">entries per page</span>',
                    'info' => 'Showing <span class="font-semibold text-gray-700">_START_</span> to <span class="font-semibold text-gray-700">_END_</span> of <span class="font-semibold text-gray-700">_TOTAL_</span> entries',
                    'paginate' => [
                        'previous' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m15 18-6-6 6-6"/></svg>',
                        'next' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4"><path d="m9 18 6-6-6-6"/></svg>',
                    ],
                ],
                'columnDefs' => [
                    // Always visible: route + totals + created + actions (indices 0-23)
                    ['targets' => [0, 1, 17, 18, 19, 20, 23], 'responsivePriority' => 1],
                    // Collapse on tablet and below
                    ['targets' => [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 21, 22], 'visible' => false, 'responsivePriority' => -1],
                ],
            ]);
    }

    protected function getColumns(): array
    {
        return [
            Column::make('pol_id')->title('POL'),
            Column::make('pod_id')->title('POD'),
            Column::make('container_type_id')->title('Container Type'),
            Column::make('feeder_id')->title('Feeder'),
            Column::make('container_size_id')->title('Size'),
            Column::make('slot_term_id')->title('Term'),
            Column::make('pod_agent_id')->title('Pod Agent'),
            Column::make('pol_agent_id')->title('Pol Agent'),
            Column::make('slot')->title('Slot'),
            Column::make('dthc')->title('DTHC'),
            Column::make('wrr')->title('WRR'),
            Column::make('ts_thc')->title('T/S THC'),
            Column::make('ts_commission')->title('T/S Commission'),
            Column::make('of')->title('OF'),
            Column::make('pod_rebate')->title('POD Rebate'),
            Column::make('free_days')->title('Free Days'),
            Column::make('total_cost')->title('Total Cost'),
            Column::make('total_collection')->title('Total Collection'),
            Column::make('net_shipping')->title('Net Shipping'),
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

    private function actionsHtml(Cost $model): string
    {
        $user = auth()->user();
        $canEdit = $user->isSuperAdmin() || $user->can('costs.edit');
        $canDelete = $user->isSuperAdmin() || $user->can('costs.delete');

        $edit = $canEdit
            ? '<a href="'.e(route('costs.edit', $model)).'" class="group inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 transition-all hover:bg-blue-500 hover:shadow-md hover:-translate-y-0.5" title="Edit">'
                .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors group-hover:text-white">'
                .'<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />'
                .'<path d="m15 5 4 4" />'
                .'</svg></a>'
            : '';

        $delete = $canDelete
            ? '<form method="POST" action="'.e(route('costs.destroy', $model)).'" class="delete-form inline-block" data-confirm="This will permanently delete this cost.">'
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
