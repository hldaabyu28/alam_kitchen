<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Menu;
use App\Models\Order;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        $totalUsers = User::count();
        $totalMenu = Menu::count();

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

        // Orders today
        $ordersTodayCount = Order::whereDate('created_at', Carbon::today())->count();

        // Revenue (All time or maybe we should default to all time as requested in views)
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');

        // Revenue this month 
        $revenueThisMonth = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // Running & Pending Orders
        $pendingOrders = Order::where('status', 'pending')->count();
        $runningOrders = Order::whereIn('status', ['processing', 'ready'])->count();

        // Recent Orders
        $recentOrders = Order::with('items')->orderBy('created_at', 'desc')->take(5)->get();
        $labels = $last7Days->pluck('day'); // Mon, Tue, dst
        $revenues = $last7Days->pluck('revenue'); // angka revenue
        $dates = $last7Days->pluck('date'); // optional (buat tooltip)
        $data = compact(
            'totalUsers',
            'totalMenu',
            'ordersTodayCount',
            'totalRevenue',
            'revenueThisMonth',
            'pendingOrders',
            'runningOrders',
            'recentOrders',
            'last7Days',
            'labels',
            'revenues',
            'dates'
        );

        if ($user->hasRole('super_admin')) {
            return view('super_admin.dashboard', $data);
        } elseif ($user->hasRole('admin')) {
            return view('admin.dashboard', $data);
        } elseif ($user->hasRole('kasir')) {
            return view('kasir.dashboard', $data);
        }

        // Fallback
        return redirect('/login');
    }
}
