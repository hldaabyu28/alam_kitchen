<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\Store;
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

        $store = Store::first();

        $isOpen = $store ? $store->is_active : true;

        $faqs = Faq::where('is_active', true)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $activeTax = \App\Models\Tax::where('is_active', true)->first();
        $taxRate = $activeTax ? $activeTax->rate : 0;

        return view('layouts.landing.index', compact('menus', 'categories', 'tables', 'store', 'faqs', 'isOpen', 'taxRate'));
    }
}
