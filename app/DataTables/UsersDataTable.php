<?php

namespace App\DataTables;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class UsersDataTable extends DataTable
{
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        return DataTables::eloquent($query)
            ->addColumn('avatar', fn (User $user) => $this->avatarHtml($user))
            ->addColumn('status', fn (User $user) => $this->statusHtml($user))
            ->addColumn('theme', fn (User $user) => $this->themeHtml($user))
            ->editColumn('created_at', fn (User $user) => $user->created_at->format('M j, Y'))
            ->addColumn('actions', fn (User $user) => $this->actionsHtml($user))
            ->rawColumns(['avatar', 'status', 'theme', 'actions']);
    }

    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->select(['id', 'name', 'email', 'email_verified_at', 'phone', 'theme', 'created_at']);
    }

    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('users-table')
            ->addTableClass('display min-w-full text-sm')
            ->columns($this->getColumns())
            ->minifiedAjax(ajaxParameters: ['headers' => ['Accept' => 'application/json, text/javascript, */*; q=0.01']])
            ->orderBy(3, 'desc')
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
                    'searchPlaceholder' => '🔍 Search users...',
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
            Column::computed('avatar')->title('User'),
            Column::computed('status')->title('Status')->orderable(false)->searchable(false),
            Column::computed('theme')->title('Theme')->orderable(false)->searchable(false),
            Column::make('created_at')->title('Joined'),
            Column::computed('actions')
                ->title('')
                ->orderable(false)
                ->searchable(false)
                ->exportable(false)
                ->printable(false)
                ->addClass('text-right'),
        ];
    }

    private function avatarHtml(User $user): string
    {
        $phoneDisplay = $user->phone ? '<div class="truncate text-xs text-topbar-muted">'.e($user->phone).'</div>' : '';

        return '<div class="flex items-center gap-3">'
            .'<div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary-400 to-primary-600 text-base font-bold text-white shadow-md">'
            .e(strtoupper(substr($user->name, 0, 1)))
            .'</div>'
            .'<div class="min-w-0">'
            .'<div class="truncate text-sm font-semibold text-topbar-text">'.e($user->name).'</div>'
            .'<div class="truncate text-xs text-topbar-muted">'.e($user->email).'</div>'
            .$phoneDisplay
            .'</div>'
            .'</div>';
    }

    private function statusHtml(User $user): string
    {
        return $user->email_verified_at
            ? '<span class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-emerald-500 to-emerald-600 px-3 py-1 text-xs font-semibold text-white shadow-sm">✓ Verified</span>'
            : '<span class="inline-flex items-center gap-1.5 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 px-3 py-1 text-xs font-semibold text-white shadow-sm">○ Unverified</span>';
    }

    private function themeHtml(User $user): string
    {
        return match ($user->theme) {
            'slate-blue' => '<span class="inline-flex items-center gap-2 rounded-full bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 shadow-sm"><span class="h-2 w-2 rounded-full bg-gradient-to-br from-blue-400 to-blue-600"></span>Blue</span>',
            'slate-teal' => '<span class="inline-flex items-center gap-2 rounded-full bg-teal-100 px-3 py-1.5 text-xs font-semibold text-teal-700 shadow-sm"><span class="h-2 w-2 rounded-full bg-gradient-to-br from-teal-400 to-teal-600"></span>Teal</span>',
            'slate-violet' => '<span class="inline-flex items-center gap-2 rounded-full bg-violet-100 px-3 py-1.5 text-xs font-semibold text-violet-700 shadow-sm"><span class="h-2 w-2 rounded-full bg-gradient-to-br from-violet-400 to-violet-600"></span>Violet</span>',
            default => '<span class="inline-flex items-center gap-2 rounded-full bg-orange-100 px-3 py-1.5 text-xs font-semibold text-orange-700 shadow-sm"><span class="h-2 w-2 rounded-full bg-gradient-to-br from-orange-400 to-orange-600"></span>Orange</span>',
        };
    }

    private function actionsHtml(User $user): string
    {
        $edit = '<a href="'.e(route('users.edit', $user)).'" class="group inline-flex items-center justify-center rounded-lg bg-blue-50 p-2 text-blue-600 transition-all hover:bg-blue-500 hover:shadow-md hover:-translate-y-0.5" title="Edit user">'
            .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors group-hover:text-white">'
            .'<path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z" />'
            .'<path d="m15 5 4 4" />'
            .'</svg></a>';

        $delete = '<form method="POST" action="'.e(route('users.destroy', $user)).'" class="delete-form inline-block" data-confirm="This will permanently delete '.e($user->name).' and all associated data.">'
            .'<input type="hidden" name="_token" value="'.csrf_token().'">'
            .'<input type="hidden" name="_method" value="DELETE">'
            .'<button type="submit" class="group inline-flex items-center justify-center rounded-lg bg-red-50 p-2 text-red-500 transition-all hover:bg-red-500 hover:shadow-md hover:-translate-y-0.5" title="Delete user">'
            .'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 transition-colors group-hover:text-white">'
            .'<path d="M3 6h18" />'
            .'<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6" />'
            .'<path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2" />'
            .'</svg></button></form>';

        return '<div class="flex items-center justify-end gap-2">'.$edit.$delete.'</div>';
    }
}
