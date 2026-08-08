<?php

namespace App\Listeners;

use Spatie\Permission\Events\PermissionAttachedEvent;
use Spatie\Permission\Events\PermissionDetachedEvent;
use Spatie\Permission\Events\RoleAttachedEvent;
use Spatie\Permission\Events\RoleDetachedEvent;
use Spatie\Permission\PermissionRegistrar;

class FlushPermissionCacheListener
{
    public function handle(
        RoleAttachedEvent|RoleDetachedEvent|PermissionAttachedEvent|PermissionDetachedEvent $event
    ): void {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
