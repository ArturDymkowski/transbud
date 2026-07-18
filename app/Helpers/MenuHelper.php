<?php

namespace App\Helpers;

class MenuHelper
{
    public static function getMainNavItems()
    {
        return [
            [
                'icon' => 'heroicon-o-user-group',
                'name' => 'Kierowcy',
                'path' => route('drivers.index'),
            ],
            [
                'icon' => 'heroicon-o-truck',
                'name' => 'Pojazdy',
                'path' => route('vehicles.index'),
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
