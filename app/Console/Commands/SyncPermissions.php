<?php

namespace App\Console\Commands;

use App\Services\PermissionSyncService;
use Illuminate\Console\Command;

class SyncPermissions extends Command
{
    protected $signature = 'permissions:sync';

    protected $description = 'Create any missing permissions and assign all permissions to the super admin and admin roles';

    public function handle(PermissionSyncService $service): int
    {
        $result = $service->sync();

        $this->info("Created {$result['created']} new permission(s).");
        $this->info("Assigned {$result['assigned']} permission(s) to the super admin and admin roles.");

        return self::SUCCESS;
    }
}
