<?php

namespace Database\Seeders;

use App\Services\PermissionSyncService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(PermissionSyncService $service): void
    {
        $service->sync();
    }
}
