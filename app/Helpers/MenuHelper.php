<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMainNavItems()
    {
        $items = [
            [
                'permission' => 'drivers.view',
                'icon' => 'heroicon-o-user-group',
                'name' => __('drivers.plural_model_label'),
                'path' => route('drivers.index'),
            ],
            [
                'permission' => 'vehicles.view',
                'icon' => 'heroicon-o-truck',
                'name' => __('vehicles.plural_model_label'),
                'path' => route('vehicles.index'),
            ],
            [
                'permission' => 'deliveries.view',
                'icon' => 'heroicon-o-clipboard-document-list',
                'name' => __('deliveries.plural_model_label'),
                'path' => route('deliveries.index'),
            ],
            [
                'permission' => 'deliveries.view',
                'icon' => 'heroicon-o-calendar-days',
                'name' => __('deliveries.calendar_title'),
                'path' => route('deliveries.calendar'),
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
            [
                'permission' => 'users.view',
                'icon' => 'heroicon-o-users',
                'name' => __('users.plural_model_label'),
                'path' => route('users.index'),
            ],
            [
                'permission' => 'roles.view',
                'icon' => 'heroicon-o-shield-check',
                'name' => __('roles.plural_model_label'),
                'path' => route('roles.index'),
            ],
            [
                'permission' => 'permissions.view',
                'icon' => 'heroicon-o-key',
                'name' => __('permissions.plural_model_label'),
                'path' => route('permissions.index'),
            ],
        ];

        return array_values(array_filter(
            $items,
            fn (array $item) => auth()->user()?->can($item['permission']) ?? false
        ));
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
}
