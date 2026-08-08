<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Navigation
    |--------------------------------------------------------------------------
    |
    | This configuration defines the navigation structure for the application.
    | Each section contains menu items with their labels, routes, and icons.
    |
    */

    'navigation' => [
        'workspace' => [
            'title' => 'Workspace',
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'dashboard',
                    'icon' => 'layout-grid',
                    'permission' => 'dashboard.view',
                ],
                [
                    'label' => 'Users',
                    'route' => 'users.index',
                    'icon' => 'users',
                    'permission' => 'users.view',
                ],
            ],
        ],
        'account' => [
            'title' => 'Account',
            'items' => [
                [
                    'label' => 'Profile',
                    'route' => 'profile.edit',
                    'icon' => 'user',
                    'permission' => 'profile.view',
                ],
            ],
        ],
        'insights' => [
            'title' => 'Insights',
            'items' => [
                [
                    'label' => 'Reports',
                    'route' => 'reports.index',
                    'icon' => 'bar-chart',
                    'permission' => 'reports.view',
                ],
                [
                    'label' => 'Settings',
                    'route' => 'settings.index',
                    'icon' => 'settings',
                    'permission' => 'settings.view',
                ],
            ],
        ],
        'administration' => [
            'title' => 'Administration',
            'items' => [
                [
                    'label' => 'Roles',
                    'route' => 'roles.index',
                    'icon' => 'shield',
                    'permission' => 'roles.view',
                ],
                [
                    'label' => 'Permissions',
                    'route' => 'permissions.index',
                    'icon' => 'key',
                    'permission' => 'permissions.view',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Screens & Permissions
    |--------------------------------------------------------------------------
    |
    | This configuration defines every screen in the application and the
    | granular actions that can be controlled via permissions. The permission
    | name for each action is stored as "{screen}.{action}".
    |
    */

    'screens' => [
        'dashboard' => [
            'label' => 'Dashboard',
            'permissions' => ['view'],
        ],
        'users' => [
            'label' => 'Users',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'roles' => [
            'label' => 'Roles',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'permissions' => [
            'label' => 'Permissions',
            'permissions' => ['view', 'edit'],
        ],
        'profile' => [
            'label' => 'Profile',
            'permissions' => ['view'],
        ],
        'reports' => [
            'label' => 'Reports',
            'permissions' => ['view'],
        ],
        'settings' => [
            'label' => 'Settings',
            'permissions' => ['view'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Super Administrator Role
    |--------------------------------------------------------------------------
    |
    | The role that owns every permission and can never be deleted.
    |
    */

    'super_admin_role' => 'superAdmin',
];
