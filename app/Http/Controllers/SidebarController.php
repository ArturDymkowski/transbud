<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SidebarController extends Controller
{
    public function getMenuData()
    {
        $menuGroups = [
            [
                'title' => 'Menu',
                'items' => [

                ],
            ],
        ];

        return view('components.sidebar', compact('menuGroups'));
    }
}
