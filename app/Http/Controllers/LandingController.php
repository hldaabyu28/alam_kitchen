<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Table;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $menus = Menu::with('category')
            ->where('is_available', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $categories = MenuCategory::where('is_active', true)
            ->orderBy('order')
            ->get();

        $tables = Table::where('is_active', true)
            ->where('status', 'available')
            ->orderBy('table_number')
            ->get();

        return view('layouts.landing.index', compact('menus', 'categories', 'tables'));
    }
}
