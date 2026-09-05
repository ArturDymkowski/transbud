<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMainNavItems()
    {
        $items = [
            [
                'icon' => 'heroicon-o-home',
                'name' => __('labels.dashboard.title'),
                'path' => route('dashboard'),
            ],
            [
                'permission' => 'deliveries.view',
                'icon' => 'heroicon-o-calendar-days',
                'name' => __('deliveries.calendar_title'),
                'path' => route('deliveries.calendar'),
            ],
            [
                'permission' => 'deliveries.view',
                'icon' => 'heroicon-o-clipboard-document-list',
                'name' => __('deliveries.plural_model_label'),
                'path' => route('deliveries.index'),
            ],
            [
                'permission' => 'drivers.view',
                'icon' => 'heroicon-o-user-group',
                'name' => __('drivers.plural_model_label'),
                'path' => route('drivers.index'),
            ],
            [
                'name' => __('labels.menu.access'),
                'subItems' => [
                    [
                        'permission' => 'users.view',
                        'icon' => 'heroicon-o-users',
                        'name' => __('users.plural_model_label'),
                        'path' => route('users.index'),
                    ],
                    [
                        'permission' => 'roles.view',
                        'icon' => 'heroicon-o-lock-closed',
                        'name' => __('roles.plural_model_label'),
                        'path' => route('roles.index'),
                    ],
                    [
                        'permission' => 'permissions.view',
                        'icon' => 'heroicon-o-shield-check',
                        'name' => __('permissions.plural_model_label'),
                        'path' => route('permissions.index'),
                    ],
                    [
                        // Not a Spatie permission on purpose — see App\Http\Middleware\EnsureSuperAdmin.
                        'superAdminOnly' => true,
                        'icon' => 'heroicon-o-finger-print',
                        'name' => __('login_audit_log.plural_model_label'),
                        'path' => route('login-audit-log.index'),
                    ],
                ],
            ],
            [
                'name' => __('labels.menu.dictionaries'),
                'subItems' => [
                    [
                        'permission' => 'vehicles.view',
                        'icon' => 'heroicon-o-truck',
                        'name' => __('vehicles.plural_model_label'),
                        'path' => route('vehicles.index'),
                    ],
                    [
                        'permission' => 'contractors.view',
                        'icon' => 'heroicon-o-building-office',
                        'name' => __('contractors.plural_model_label'),
                        'path' => route('contractors.index'),
                    ],
                    [
                        'permission' => 'contractor-addresses.view',
                        'icon' => 'heroicon-o-book-open',
                        'name' => __('address_book.plural_model_label'),
                        'path' => route('contractor-addresses.index'),
                    ],
                    [
                        'permission' => 'goods.view',
                        'icon' => 'heroicon-o-cube',
                        'name' => __('goods.plural_model_label'),
                        'path' => route('goods.index'),
                    ],
                    [
                        'permission' => 'units.view',
                        'icon' => 'heroicon-o-scale',
                        'name' => __('units.plural_model_label'),
                        'path' => route('units.index'),
                    ],
                ],
            ],
        ];

        return array_values(array_filter(array_map(function (array $item) {
            if (isset($item['subItems'])) {
                $item['subItems'] = array_values(array_filter($item['subItems'], [self::class, 'isVisible']));

                return $item['subItems'] ? $item : null;
            }

            return self::isVisible($item) ? $item : null;
        }, $items)));
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => '',
                'items' => self::getMainNavItems(),
            ],
        ];
    }

    private static function isVisible(array $item): bool
    {
        if (! empty($item['superAdminOnly'])) {
            return (bool) auth()->user()?->is_super_admin;
        }

        return empty($item['permission']) || (auth()->user()?->can($item['permission']) ?? false);
    }
}
