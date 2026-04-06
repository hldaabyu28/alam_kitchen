@extends('layouts.app')

@section('title', 'Analisis Penjualan & Performa')

@section('content')
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4 animate-fadeIn">
        <div>
            <h2 class="text-2xl font-bold">Laporan Analisis 📈</h2>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Pantau performa penjualan dan pesanan Anda dalam 7 hari terakhir.</p>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800 mb-8 animate-fadeIn">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Pendapatan 7 Hari Terakhir</h3>
        </div>

        <div class="flex items-end justify-between gap-2 h-64 mt-4">
            @foreach($last7Days as $data)
            <div class="flex-1 flex flex-col items-center group relative">
                <div class="w-full bg-emerald-600 rounded-t-xl transition-all duration-300 hover:bg-emerald-500 relative" 
                     style="height: {{ $data['percentage'] > 0 ? $data['percentage'] : 5 }}%;">
                    
                    <!-- Tooltip -->
                    <div class="absolute -top-10 left-1/2 -translate-x-1/2 bg-gray-800 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
                        Rp {{ number_format($data['revenue'], 0, ',', '.') }}
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2 font-medium">{{ $data['day'] }}</p>
                <p class="text-[10px] text-gray-400">{{ $data['date'] }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-8 mb-8 animate-fadeIn">
        <!-- Best Selling Items -->
        <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
            <h3 class="text-xl font-bold mb-6">Menu Terlaris (Top 5)</h3>
            
            <div class="space-y-4">
                @forelse($topItems as $index => $item)
                <div class="flex items-center gap-4 p-3 hover:bg-gray-50 dark:hover:bg-gray-800 rounded-xl transition">
                    <div class="w-10 h-10 rounded-full {{ $index === 0 ? 'bg-yellow-100 text-yellow-600' : ($index === 1 ? 'bg-gray-200 text-gray-600' : ($index === 2 ? 'bg-orange-100 text-orange-600' : 'bg-emerald-100 text-emerald-600')) }} flex items-center justify-center font-bold">
                        #{{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-900 dark:text-white truncate">
                            {{ $item->menu ? $item->menu->nama : 'Menu Dihapus' }}
                        </p>
                        <p class="text-xs text-gray-500">Terjual: {{ $item->total_sold }} porsi</p>
                    </div>
                    <div class="font-bold text-emerald-600">
                        @if($item->menu)
                            Rp {{ number_format($item->menu->harga * $item->total_sold, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-6 text-gray-500">Belum ada data penjualan.</div>
                @endforelse
            </div>
        </div>

        <!-- Order & Payment Breakdown -->
        <div class="space-y-8">
            <!-- Order Statuses -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
                <h3 class="text-xl font-bold mb-6">Status Pesanan Keseluruhan</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-xl border border-yellow-100 dark:border-yellow-800/30 text-center">
                        <h4 class="text-xl font-bold text-yellow-600 dark:text-yellow-400">{{ $orderStatuses->get('pending', 0) }}</h4>
                        <p class="text-xs text-yellow-600/80 dark:text-yellow-400/80 uppercase mt-1">Pending</p>
                    </div>
                    <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-xl border border-blue-100 dark:border-blue-800/30 text-center">
                        <h4 class="text-xl font-bold text-blue-600 dark:text-blue-400">{{ $orderStatuses->get('processing', 0) }}</h4>
                        <p class="text-xs text-blue-600/80 dark:text-blue-400/80 uppercase mt-1">Diproses</p>
                    </div>
                    <div class="bg-emerald-50 dark:bg-emerald-900/20 p-4 rounded-xl border border-emerald-100 dark:border-emerald-800/30 text-center">
                        <h4 class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ $orderStatuses->get('completed', 0) }}</h4>
                        <p class="text-xs text-emerald-600/80 dark:text-emerald-400/80 uppercase mt-1">Selesai</p>
                    </div>
                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-xl border border-red-100 dark:border-red-800/30 text-center">
                        <h4 class="text-xl font-bold text-red-600 dark:text-red-400">{{ $orderStatuses->get('cancelled', 0) }}</h4>
                        <p class="text-xs text-red-600/80 dark:text-red-400/80 uppercase mt-1">Dibatalkan</p>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="bg-white dark:bg-gray-900 p-6 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800">
                <h3 class="text-xl font-bold mb-6">Metode Pembayaran (Pesanan Selesai)</h3>
                <div class="flex flex-col gap-3">
                    @forelse($paymentMethods as $method => $count)
                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex items-center gap-3">
                                <span class="bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-300 w-8 h-8 rounded flex items-center justify-center font-bold uppercase text-xs">
                                    {{ substr($method, 0, 2) }}
                                </span>
                                <span class="font-medium text-gray-700 dark:text-gray-300 capitalize">{{ str_replace('_', ' ', $method) }}</span>
                            </div>
                            <span class="font-bold text-gray-900 dark:text-white">{{ $count }} trx</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">Belum ada data metode pembayaran.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
