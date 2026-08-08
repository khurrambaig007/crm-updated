<?php

namespace App\DataTables;

use App\Models\Currency;
use App\Services\ExchangeRateService;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class CurrencyExchangeRatesDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return DataTables::eloquent($query)
            ->editColumn('exchange_rate_date', fn (Currency $model) => $model->exchange_rate_date?->format('M j, Y'))
            ->addColumn('rates', fn (Currency $model) => $this->ratesHtml($model))
            ->addColumn('actions', fn (Currency $model) => $this->actionsHtml($model))
            ->rawColumns(['rates', 'actions']);
    }

    public function query(Currency $model): QueryBuilder
    {
        return $model->newQuery()->select(['id', 'exchange_rate_date', 'exchange_rate']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('currency-exchange-rates-table')
            ->addTableClass('display min-w-full text-sm')
            ->columns($this->getColumns())
            ->minifiedAjax(ajaxParameters: ['headers' => ['Accept' => 'application/json, text/javascript, */*; q=0.01']])
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
                    'searchPlaceholder' => 'Search exchange rates...',
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
            Column::make('exchange_rate_date')->title('Date'),
            Column::computed('rates')
                ->title('Rates')
                ->orderable(false)
                ->searchable(false),
            Column::computed('actions')
                ->title('')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('text-right'),
        ];
    }

    private function ratesHtml(Currency $model): string
    {
        $rates = json_decode($model->exchange_rate ?? '', true) ?: [];
        $chips = '';

        foreach (ExchangeRateService::CURRENCIES as $code) {
            $value = $rates[$code] ?? null;

            if ($value === null) {
                continue;
            }

            $formatted = rtrim(rtrim(number_format((float) $value, 4), '0'), '.');
            $chips .= '<span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-semibold text-blue-700 ring-1 ring-inset ring-blue-600/10">'.$code.' '.$formatted.'</span>';
        }

        return '<div class="flex flex-wrap gap-1.5">'.$chips.'</div>';
    }

    private function actionsHtml(Currency $model): string
    {
        $user = auth()->user();
        $canEdit = $user->isSuperAdmin() || $user->can('currencies.edit');

        $edit = $canEdit
            ? '<a href="'.e(route('currency-exchange-rates.edit', $model)).'" class="group inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 transition-all hover:bg-blue-500 hover:shadow-md hover:-translate-y-0.5" title="Edit">'
                .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors group-hover:text-white">'
                .'<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />'
                .'<path d="m15 5 4 4" />'
                .'</svg></a>'
            : '';

        return '<div class="flex items-center justify-end gap-2">'.$edit.'</div>';
    }
}
