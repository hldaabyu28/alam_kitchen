<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuCategory;
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

        return view('layouts.landing.index', compact('menus', 'categories'));
    }
}
