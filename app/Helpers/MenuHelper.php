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
