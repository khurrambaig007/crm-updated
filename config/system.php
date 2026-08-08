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
                [
                    'label' => 'System Operations',
                    'route' => 'system-operations.index',
                    'icon' => 'cpu',
                    'permission' => 'system_operations.view',
                    'children' => [
                        ['label' => 'Container Size', 'route' => 'container-sizes.index', 'icon' => 'box', 'permission' => 'container_sizes.view'],
                        ['label' => 'Port Location', 'route' => 'port-locations.index', 'icon' => 'anchor', 'permission' => 'port_locations.view'],
                        ['label' => 'Carrier', 'route' => 'carriers.index', 'icon' => 'ship', 'permission' => 'carriers.view'],
                        ['label' => 'Agent', 'route' => 'agents.index', 'icon' => 'user', 'permission' => 'agents.view'],
                        ['label' => 'Container Type', 'route' => 'container-types.index', 'icon' => 'layers', 'permission' => 'container_types.view'],
                        ['label' => 'Container Kind', 'route' => 'container-kinds.index', 'icon' => 'box', 'permission' => 'container_kinds.view'],
                        ['label' => 'Commodity', 'route' => 'commodities.index', 'icon' => 'tag', 'permission' => 'commodities.view'],
                        ['label' => 'Vessel Voyage', 'route' => 'vessel-voyages.index', 'icon' => 'ship-wheel', 'permission' => 'vessel_voyages.view'],
                        ['label' => 'Charge', 'route' => 'charges.index', 'icon' => 'badge-dollar-sign', 'permission' => 'charges.view'],
                        ['label' => 'Slot', 'route' => 'slots.index', 'icon' => 'list', 'permission' => 'slots.view'],
                        ['label' => 'Investor', 'route' => 'investors.index', 'icon' => 'wallet', 'permission' => 'investors.view'],
                        ['label' => 'PA Party', 'route' => 'parties.index', 'icon' => 'building', 'permission' => 'parties.view'],
                        ['label' => 'Settlement Type', 'route' => 'settlement-types.index', 'icon' => 'receipt', 'permission' => 'settlement_types.view'],
                        ['label' => 'Shipper / BP', 'route' => 'shipper-bps.index', 'icon' => 'package', 'permission' => 'shipper_bps.view'],
                        ['label' => 'Suppliers', 'route' => 'suppliers.index', 'icon' => 'truck', 'permission' => 'suppliers.view'],
                        ['label' => 'Sub Companies', 'route' => 'sub-companies.index', 'icon' => 'landmark', 'permission' => 'sub_companies.view'],
                    ],
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
        'system_operations' => [
            'label' => 'System Operations',
            'permissions' => ['view'],
        ],
        'container_sizes' => [
            'label' => 'Container Size',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'port_locations' => [
            'label' => 'Port Location',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'carriers' => [
            'label' => 'Carrier',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'agents' => [
            'label' => 'Agent',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'container_types' => [
            'label' => 'Container Type',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'container_kinds' => [
            'label' => 'Container Kind',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'commodities' => [
            'label' => 'Commodity',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'vessel_voyages' => [
            'label' => 'Vessel Voyage',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'charges' => [
            'label' => 'Charge',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'slots' => [
            'label' => 'Slot',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'investors' => [
            'label' => 'Investor',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'parties' => [
            'label' => 'PA Party',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'settlement_types' => [
            'label' => 'Settlement Type',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'shipper_bps' => [
            'label' => 'Shipper / BP',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'suppliers' => [
            'label' => 'Supplier',
            'permissions' => ['view', 'add', 'edit', 'delete'],
        ],
        'sub_companies' => [
            'label' => 'Sub Company',
            'permissions' => ['view', 'add', 'edit', 'delete'],
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
