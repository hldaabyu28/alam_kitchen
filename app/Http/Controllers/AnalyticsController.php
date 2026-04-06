<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnalyticsController extends Controller
{
    public function index()
    {
        // 1. Daily Revenue (last 7 days)
        $last7Days = collect();
        $maxRevenue = 0;
        
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = Order::whereDate('created_at', $date)
                ->where('payment_status', 'paid')
                ->sum('total_amount');
                
            $maxRevenue = max($maxRevenue, $revenue);
            
            $last7Days->push([
                'day' => $date->format('D'), // Mon, Tue...
                'date' => $date->format('d M'),
                'revenue' => $revenue,
                'percentage' => 0 // calculated later
            ]);
        }
        
        if ($maxRevenue > 0) {
            $last7Days->transform(function ($item) use ($maxRevenue) {
                $item['percentage'] = ($item['revenue'] / $maxRevenue) * 100;
                return $item;
            });
        }
        
        // 2. Best Selling Items (Top 5)
        $topItems = OrderItem::select('menu_id', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('menu_id')
            ->orderBy('total_sold', 'desc')
            ->with('menu')
            ->take(5)
            ->get();
            
        // 3. Order Status Breakdown
        $orderStatuses = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
            
        // 4. Payment Methods Breakdown
        $paymentMethods = Order::select('payment_method', DB::raw('count(*) as count'))
            ->whereNotNull('payment_method')
            ->groupBy('payment_method')
            ->get()
            ->pluck('count', 'payment_method');

        $data = compact('last7Days', 'topItems', 'orderStatuses', 'paymentMethods', 'maxRevenue');
        
        $role = Auth::user()->hasRole('super_admin') ? 'super_admin' : 'admin';
        return view($role . '.analytics.index', $data);
    }
}
