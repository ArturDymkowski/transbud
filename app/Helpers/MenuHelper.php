<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMainNavItems()
    {
        return [
            [
                'icon' => 'heroicon-o-user-group',
                'name' => __('drivers.plural_model_label'),
                'path' => route('drivers.index'),
            ],
            [
                'icon' => 'heroicon-o-truck',
                'name' => __('vehicles.plural_model_label'),
                'path' => route('vehicles.index'),
            ],
            [
                'icon' => 'heroicon-o-building-office',
                'name' => __('contractors.plural_model_label'),
                'path' => route('contractors.index'),
            ],
            [
                'icon' => 'heroicon-o-book-open',
                'name' => __('address_book.plural_model_label'),
                'path' => route('contractor-addresses.index'),
            ],
        ];
    }

    public static function getMenuGroups()
    {
        return [
            [
                'title' => '',
                'items' => self::getMainNavItems()
            ],
        ];
    }
}
