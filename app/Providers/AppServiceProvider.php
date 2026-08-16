<?php

namespace App\Providers;

use App\Services\PermissionSyncService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Yajra\DataTables\Html\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Builder::useVite();

        $this->syncPermissionsIfNeeded();
    }

    /**
     * Automatically create missing permissions and assign them to the super admin role.
     *
     * This runs only when the permission tables exist and a configured permission
     * is missing, keeping the per-request overhead minimal.
     */
    private function syncPermissionsIfNeeded(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        try {
            $service = app(PermissionSyncService::class);

            if ($service->hasMissingPermissions()) {
                $service->sync();
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
