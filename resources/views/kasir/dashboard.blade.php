@extends('layouts.app')

@section('title', 'Kasir Dashboard')

@section('content')
<!-- Welcome Banner -->
<div class="mb-8">
    <h2 class="text-2xl font-bold">Selamat Datang, {{ Auth::user()->name }} 👋</h2>
    <p class="text-gray-500 dark:text-gray-400 mt-1">Role: <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">Kasir</span></p>
</div>

<!-- Stats Cards -->
<div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6 mb-8 animate-fadeIn">
    <!-- Transaksi Hari Ini -->
    <div
        class="bg-gradient-to-br from-emerald-700 to-emerald-600 text-white p-6 rounded-3xl shadow-lg relative overflow-hidden">
        <div class="pattern-dots absolute inset-0 opacity-10"></div>
        <div class="relative z-10">
            <div class="flex justify-between items-start mb-4">
                <p class="text-emerald-100 font-medium">Transaksi Hari Ini</p>
                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <h3 class="text-5xl font-bold mb-2">{{ $ordersTodayCount }}</h3>
            <div class="flex items-center gap-2 text-sm">
                <span class="bg-emerald-500/30 px-2 py-1 rounded-lg flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span>+12% dari kemarin</span>
                </span>
            </div>
        </div>
    </div>

    <!-- Pesanan Berjalan -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-start mb-4">
            <p class="text-gray-600 dark:text-gray-400 font-medium">Pesanan Berjalan</p>
            <div
                class="w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
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

    <!-- Pending -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-start mb-4">
            <p class="text-gray-600 dark:text-gray-400 font-medium">Menunggu Pembayaran</p>
            <div
                class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900 text-yellow-600 dark:text-yellow-300 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
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

<!-- Main Content Row -->
<div class="grid lg:grid-cols-3 gap-6 mb-8 animate-fadeIn">

    <!-- Antrian Pesanan / Order Queue -->
    <div
        class="lg:col-span-2 bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Antrian Pesanan</h3>
            <button class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">Lihat Semua</button>
        </div>

        <div class="space-y-4">
            @forelse($recentOrders as $order)
            <div class="flex items-center gap-4 p-4 {{ in_array($order->status, ['processing', 'ready']) ? 'bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800' : 'bg-gray-50 dark:bg-gray-800' }} rounded-xl">
                <div class="w-12 h-12 {{ in_array($order->status, ['processing', 'ready']) ? 'bg-blue-100 dark:bg-blue-900' : 'bg-gray-200 dark:bg-gray-700' }} rounded-xl flex items-center justify-center">
                    <span class="text-2xl">🧾</span>
                </div>
                <div class="flex-1">
                    <h4 class="font-semibold">{{ $order->customer_name ?? 'Pelanggan' }} — {{ $order->items->count() ?? 0 }} Item</h4>
                    <p class="text-sm text-gray-500">{{ $order->order_number }} • {{ $order->created_at->diffForHumans() }}</p>
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
            <div class="p-4 text-center text-gray-500 dark:text-gray-400">Belum ada antrian pesanan</div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Aksi Cepat</h3>
        </div>

        <div class="space-y-3">
            <a href="{{ route('kasir.transaksi.index') }}"
                class="w-full flex items-center gap-3 p-4 bg-emerald-700 text-white rounded-xl hover:bg-emerald-800 transition">
                <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                </div>
                <span class="font-medium">Transaksi Baru</span>
            </a>

            <a href="{{ route('kasir.menu.index') }}"
                class="w-full flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <div
                    class="w-10 h-10 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-300 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                </div>
                <span class="font-medium">Lihat Menu</span>
            </a>

            <a href="{{ route('kasir.orders.index') }}"
                class="w-full flex items-center gap-3 p-4 bg-gray-50 dark:bg-gray-800 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                <div
                    class="w-10 h-10 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-300 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <span class="font-medium">Proses Pembayaran</span>
            </a>
        </div>
    </div>
</div>
@endsection