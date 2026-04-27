@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="mb-8">
    <h2 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->name }} 👋</h2>
    <p class="text-gray-500 dark:text-gray-400 mt-1">Role: <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">Admin</span></p>
</div>

<!-- Stats Cards -->
<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-6 mb-8 animate-fadeIn">
    <!-- Total Menu Card -->
    <div
        class="bg-gradient-to-br from-emerald-700 to-emerald-600 text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
        <div class="pattern-dots absolute inset-0 opacity-10"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <p class="text-emerald-100 font-medium">Total Menu</p>
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
            </div>
            <h3 class="text-5xl font-bold mb-2">{{ $totalMenu }}</h3>
            <div class="flex items-center gap-2 text-sm">
                <span class="bg-emerald-500/30 px-2 py-1 rounded-lg flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span>Bertambah dari bulan lalu</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Orders Today Card -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-start mb-4">
            <p class="text-gray-600 dark:text-gray-400 font-medium">Pesanan Hari Ini</p>
            <div
                class="w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-5xl font-bold mb-2">{{ $ordersTodayCount }}</h3>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span
                class="bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 px-2 py-1 rounded-lg flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
                <span>+12% dari kemarin</span>
            </span>
        </div>
    </div>

    <!-- Running Orders Card -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-start mb-4">
            <p class="text-gray-600 dark:text-gray-400 font-medium">Pesanan Berjalan</p>
            <div
                class="w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-5xl font-bold mb-2">{{ $runningOrders }}</h3>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span class="bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 px-2 py-1 rounded-lg">
                Sedang Diproses
            </span>
        </div>
    </div>

    <!-- Pending Orders Card -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-start mb-4">
            <p class="text-gray-600 dark:text-gray-400 font-medium">Pesanan Pending</p>
            <div
                class="w-8 h-8 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-5xl font-bold mb-2">{{ $pendingOrders }}</h3>
        <div class="flex items-center gap-2 text-sm text-gray-500">
            <span class="bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 px-2 py-1 rounded-lg">
                Menunggu
            </span>
        </div>
    </div>
</div>

<!-- Charts & Widgets Row -->
<div class="grid lg:grid-cols-3 gap-6 mb-8 animate-fadeIn">

    <!-- Sales Analytics Chart -->
    <div
        class="lg:col-span-2 bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Analisis Penjualan</h3>
            <select
                class="px-4 py-2 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-sm">
                <option>Minggu Ini</option>
                <option>Bulan Ini</option>
                <option>Tahun Ini</option>
            </select>
        </div>

        <!-- Chart Bars -->
        <div class="flex items-end justify-between gap-2 h-64">
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full h-20 bg-gray-200 dark:bg-gray-800 pattern-dots rounded-t-xl"></div>
                <p class="text-xs text-gray-500 mt-2">S</p>
            </div>
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full h-32 bg-emerald-600 rounded-t-xl"></div>
                <p class="text-xs text-gray-500 mt-2">M</p>
            </div>
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full h-28 bg-emerald-500 rounded-t-xl"></div>
                <p class="text-xs text-gray-500 mt-2">T</p>
            </div>
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full h-48 bg-emerald-700 rounded-t-xl relative">
                    <span
                        class="absolute -top-6 left-1/2 -translate-x-1/2 text-xs font-semibold bg-emerald-700 text-white px-2 py-1 rounded">75%</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">W</p>
            </div>
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full h-24 bg-gray-200 dark:bg-gray-800 pattern-dots rounded-t-xl"></div>
                <p class="text-xs text-gray-500 mt-2">T</p>
            </div>
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full h-16 bg-gray-200 dark:bg-gray-800 pattern-dots rounded-t-xl"></div>
                <p class="text-xs text-gray-500 mt-2">F</p>
            </div>
            <div class="flex-1 flex flex-col items-center">
                <div class="w-full h-12 bg-gray-200 dark:bg-gray-800 pattern-dots rounded-t-xl"></div>
                <p class="text-xs text-gray-500 mt-2">S</p>
            </div>
        </div>
    </div>

    <!-- Revenue Card -->
    <div
        class="bg-gradient-to-br from-blue-700 to-blue-600 text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
        <div class="pattern-dots absolute inset-0 opacity-10"></div>
        <div class="relative z-10">
            <h3 class="text-lg font-semibold mb-2">Total Pendapatan</h3>
            <p class="text-4xl font-bold mb-6">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }}</p>

            <!-- Progress Circle -->
            <div class="relative w-32 h-32 mx-auto mb-6">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="64" cy="64" r="56" stroke="rgba(255,255,255,0.2)" stroke-width="8"
                        fill="none" />
                    <circle cx="64" cy="64" r="56" stroke="white" stroke-width="8" fill="none"
                        stroke-dasharray="352" stroke-dashoffset="88" stroke-linecap="round" />
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-3xl font-bold">75%</span>
                </div>
            </div>

            <p class="text-sm text-blue-100 text-center">Target tercapai</p>
        </div>
    </div>
</div>

<!-- Bottom Row -->
<div class="grid lg:grid-cols-3 gap-6 animate-fadeIn">

    <!-- Recent Orders -->
    <div
        class="lg:col-span-2 bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Pesanan Terbaru</h3>
            <button class="text-sm text-emerald-600 hover:text-emerald-700">Lihat Semua</button>
        </div>

        <div class="space-y-4">
            @forelse($recentOrders as $order)
            <div class="flex items-center gap-4 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900 rounded-xl flex items-center justify-center">
                    <span class="text-2xl">📋</span>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold">{{ $order->customer_name ?? 'Pelanggan' }}</h4>
                    <p class="text-sm text-gray-500">{{ $order->order_number }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    @if($order->status === 'completed')
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Selesai</span>
                    @elseif(in_array($order->status, ['processing', 'ready']))
                    <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Diproses</span>
                    @else
                    <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-1 rounded-full">Pending</span>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-4 text-center text-gray-500 dark:text-gray-400">Belum ada pesanan terbaru</div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Aksi Cepat</h3>
        </div>

        <div class="space-y-3">
            <a href="{{ route('admin.menu.index') }}"
                class="w-full flex items-center gap-3 p-4 bg-emerald-700 text-white rounded-xl hover:bg-emerald-800 transition">

                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>

                <span class="font-medium">Tambah Menu</span>
            </a>

            <a href="{{ route('admin.orders.index') }}"
                class="w-full flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <div
                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <span class="font-medium">Kelola Pesanan</span>
            </a>

            <a href="{{ route('admin.analytics.index') }}"
                class="w-full flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <div
                    class="w-10 h-10 bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
                <span class="font-medium">Lihat Laporan</span>
            </a>
        </div>
    </div>
</div>
@endsection