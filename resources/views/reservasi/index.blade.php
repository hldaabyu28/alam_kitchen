@extends('layouts.app')

@section('title', 'Kelola Reservasi')
@section('content')
@php
$user = Auth::user();
$role = 'kasir';
if ($user && $user->hasRole('super_admin')) {
$role = 'super_admin';
} elseif ($user && $user->hasRole('admin')) {
$role = 'admin';
}
@endphp

<!-- Notifications -->
@if (session('success'))
<div id="notification"
    class="fixed top-24 right-4 bg-emerald-600 text-white px-6 py-4 rounded-2xl shadow-lg z-50 flex items-center gap-3 animate-slideIn">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <p>{{ session('success') }}</p>
</div>
@endif
@if (session('error'))
<div id="notification"
    class="fixed top-24 right-4 bg-red-600 text-white px-6 py-4 rounded-2xl shadow-lg z-50 flex items-center gap-3 animate-slideIn">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
    </svg>
    <p>{{ session('error') }}</p>
</div>
@endif

<!-- Page Header -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
    <div>
        <h2 class="text-3xl sm:text-4xl font-bold mb-2">Kelola Reservasi</h2>
        <p class="text-gray-500 dark:text-gray-400">Kelola reservasi meja dan pesanan pelanggan</p>
    </div>
    <button onclick="openCreateModal()"
        class="bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-xl font-semibold transition flex items-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Reservasi
    </button>
</div>

<!-- Filter Tabs -->
@php
$statuses = [
'all' => 'Semua',
'pending' => 'Pending',
'confirmed' => 'Dikonfirmasi',
'cancelled' => 'Dibatalkan',
'completed' => 'Selesai',
];
$currentStatus = request('status', 'all');
@endphp

<div class="flex gap-2 mb-6 overflow-x-auto pb-2">
    @foreach ($statuses as $key => $label)
    @php
    $url = $key === 'all' ? url()->current() : url()->current() . '?status=' . $key;
    $isActive = $currentStatus === $key || ($key === 'all' && !request('status'));
    @endphp
    <a href="{{ $url }}"
        class="px-6 py-2 rounded-full font-medium whitespace-nowrap transition
                    {{ $isActive ? 'bg-emerald-700 text-white' : 'bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700' }}">
        {{ $label }}
        @if ($key === 'all')
        ({{ $reservations->count() }})
        @endif
    </a>
    @endforeach
</div>

<!-- Reservation Table -->
<div class="bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <tr>
                    <th class="px-6 py-4 text-sm font-semibold">ID</th>
                    <th class="px-6 py-4 text-sm font-semibold">Customer</th>
                    <th class="px-6 py-4 text-sm font-semibold">Meja</th>
                    <th class="px-6 py-4 text-sm font-semibold">Tanggal & Waktu</th>
                    <th class="px-6 py-4 text-sm font-semibold">Tamu</th>
                    <th class="px-6 py-4 text-sm font-semibold">Pesanan</th>
                    <th class="px-6 py-4 text-sm font-semibold">Status</th>
                    <th class="px-6 py-4 text-sm font-semibold text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                @forelse ($reservations as $reservation)
                @php
                $statusColors = [
                'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                'confirmed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                'completed' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                ];
                $statusLabels = [
                'pending' => 'Pending',
                'confirmed' => 'Dikonfirmasi',
                'cancelled' => 'Dibatalkan',
                'completed' => 'Selesai',
                ];
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <td class="px-6 py-4">
                        <p class="font-mono font-bold text-sm">#{{ $reservation->id }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-semibold">{{ $reservation->customer_name }}</p>
                            <p class="text-sm text-gray-500">{{ $reservation->customer_phone }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @if ($reservation->table)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300">
                            Meja #{{ $reservation->table->table_number }}
                        </span>
                        @else
                        <span class="text-gray-400 text-sm">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <p>{{ $reservation->reservation_date?->format('d M Y') }}</p>
                            <p class="text-gray-400">{{ $reservation->reservation_time_slot }}</p>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">
                            {{ $reservation->guest_count }} orang
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                            {{ $reservation->orders->count() }} pesanan
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$reservation->status] ?? '' }}">
                            {{ $statusLabels[$reservation->status] ?? $reservation->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <button onclick="viewReservation({{ $reservation->id }})"
                                class="px-4 py-2 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded-xl text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-800 transition">
                                Detail
                            </button>
                            @if($reservation->customer_phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $reservation->customer_phone) }}?text=Halo%20{{ urlencode($reservation->customer_name) }},%20reservasi%20Anda%20pada%20tanggal%20{{ \Carbon\Carbon::parse($reservation->reservation_date)->format('d%20M%20Y') }}%20telah%20diperbarui%20menjadi%20{{ $reservation->status }}." target="_blank"
                                    class="px-4 py-2 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded-xl text-sm font-medium hover:bg-green-200 dark:hover:bg-green-800 transition flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.573-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86s.274.072.376-.043c.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.045.072.045.419-.1.824zm-3.423-14.416c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm.029 18.88c-1.161 0-2.305-.292-3.318-.844l-3.677.964.984-3.595c-.607-1.052-.927-2.246-.926-3.468.005-3.825 3.116-6.937 6.941-6.938 1.851.001 3.594.726 4.903 2.037 1.31 1.311 2.032 3.056 2.03 4.908-.005 3.826-3.116 6.936-6.937 6.936z"></path></svg>
                                    WA
                                </a>
                            @endif
                            @if (!in_array($reservation->status, ['completed', 'cancelled']))
                            <button onclick="openStatusModal({{ $reservation->id }}, '{{ $reservation->status }}')"
                                class="px-4 py-2 bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 rounded-xl text-sm font-medium hover:bg-emerald-200 dark:hover:bg-emerald-800 transition">
                                Update
                            </button>
                            @endif
                            @if ($reservation->status === 'confirmed')
                            <button onclick="openOrderModal({{ $reservation->id }})"
                                class="px-4 py-2 bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300 rounded-xl text-sm font-medium hover:bg-amber-200 dark:hover:bg-amber-800 transition">
                                + Pesanan
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">Belum ada reservasi</h3>
                        <p class="text-gray-500">Klik "Tambah Reservasi" untuk membuat reservasi baru</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ==================== CREATE RESERVATION MODAL ==================== -->
<div id="create-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto animate-slideIn">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Tambah Reservasi Baru</h3>
            <button onclick="closeCreateModal()"
                class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="create-form" method="POST" action="{{ route($role . '.reservasi.store') }}">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nama Customer *</label>
                    <input type="text" name="customer_name" required
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="Nama lengkap" />
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">No. Telepon *</label>
                        <input type="tel" name="customer_phone" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            placeholder="08xxxxxxxxxx" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="customer_email"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            placeholder="email@contoh.com" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Tanggal *</label>
                        <input type="date" name="reservation_date" required min="{{ date('Y-m-d') }}"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Waktu *</label>
                        <input type="time" name="reservation_time_slot" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Jumlah Tamu *</label>
                        <input type="number" name="guest_count" required min="1" value="1"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Pilih Meja *</label>
                        <select name="table_id" required id="table-select"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">-- Pilih Meja --</option>
                            @foreach ($tables as $table)
                            <option value="{{ $table->id }}" data-capacity="{{ $table->capacity }}">
                                Meja #{{ $table->table_number }} (Kapasitas: {{ $table->capacity }})
                                {{ $table->location ? '- ' . $table->location : '' }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Permintaan Khusus</label>
                    <textarea name="special_requests" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="Catatan khusus..."></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Buat Reservasi
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ==================== DETAIL MODAL ==================== -->
<div id="detail-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-2xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto animate-slideIn">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold">Detail Reservasi</h3>
            <button onclick="closeDetailModal()"
                class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="detail-content">
            <p class="text-center text-gray-500 py-8">Memuat...</p>
        </div>
    </div>
</div>

<!-- ==================== STATUS UPDATE MODAL ==================== -->
<div id="status-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8 animate-slideIn">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Update Status Reservasi</h3>
            <button onclick="closeStatusModal()"
                class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <form id="status-form" method="POST">
            @csrf
            @method('PATCH')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium mb-2">Status Baru</label>
                    <select name="status" id="rsv-status-select" required onchange="toggleRsvCancelReason()"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option value="confirmed">✅ Dikonfirmasi</option>
                        <option value="completed">🎉 Selesai</option>
                        <option value="cancelled">❌ Dibatalkan</option>
                    </select>
                </div>
                <div id="rsv-cancel-reason-field" class="hidden">
                    <label class="block text-sm font-medium mb-2">Alasan Pembatalan *</label>
                    <textarea name="cancel_reason" id="rsv-cancel-reason" rows="3"
                        class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        placeholder="Masukkan alasan pembatalan..."></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit"
                        class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-xl font-semibold transition">
                        Simpan
                    </button>
                    <button type="button" onclick="closeStatusModal()"
                        class="px-6 py-3 border border-gray-300 dark:border-gray-700 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                        Batal
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ==================== ADD ORDER MODAL ==================== -->
<div id="order-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-5xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto animate-slideIn">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Tambah Pesanan ke Reservasi</h3>
            <button onclick="closeOrderModal()"
                class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="flex flex-col lg:flex-row gap-6" style="max-height: 70vh;">
            <!-- Menu Grid -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <div class="relative mb-3">
                    <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <input type="text" id="rsv-search-menu" placeholder="Cari menu..." oninput="rsvFilterMenus()"
                        class="w-full pl-12 pr-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                </div>
                <div class="flex gap-2 mb-3 overflow-x-auto pb-2">
                    <button onclick="rsvFilterByCategory('all')" data-rsvcat="all"
                        class="rsv-cat-btn px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap bg-emerald-700 text-white">
                        Semua
                    </button>
                    @foreach ($categories as $category)
                    <button onclick="rsvFilterByCategory('{{ $category->id }}')" data-rsvcat="{{ $category->id }}"
                        class="rsv-cat-btn px-4 py-2 rounded-full text-sm font-medium whitespace-nowrap bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700">
                        {{ $category->name }}
                    </button>
                    @endforeach
                </div>
                <div id="rsv-menu-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 overflow-y-auto flex-1 pb-4">
                    @foreach ($menus as $menu)
                    <div
                        class="rsv-menu-item group bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer transition-all duration-200 hover:shadow-xl hover:-translate-y-1 hover:border-emerald-500"
                        data-id="{{ $menu->id }}"
                        data-name="{{ $menu->name }}"
                        data-price="{{ $menu->price }}"
                        data-category="{{ $menu->category_id }}"
                        data-stock="{{ $menu->stock }}"
                        onclick="rsvAddToCart({{ $menu->id }})">
                        <!-- Image -->
                        <div class="relative">
                            @if ($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}"
                                alt="{{ $menu->name }}"
                                class="w-full h-28 object-cover">
                            @else
                            <div class="w-full h-28 bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14">
                                    </path>
                                </svg>
                            </div>
                            @endif

                            <!-- Hover overlay -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition"></div>
                        </div>

                        <!-- Content -->
                        <div class="p-3 space-y-1">
                            <!-- Name -->
                            <h4 class="font-semibold text-sm line-clamp-2 leading-tight">
                                {{ $menu->name }}
                            </h4>

                            <!-- Price -->
                            <p class="text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                                Rp {{ number_format($menu->price, 0, ',', '.') }}
                            </p>

                            <!-- Stock -->
                            @if ($menu->stock !== null)
                            <p class="text-xs text-gray-400">
                                Stok: {{ $menu->stock }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Cart Panel -->
            <div class="w-full lg:w-80 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 flex flex-col overflow-hidden">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h4 class="font-bold flex items-center gap-2">
                        🛒 Keranjang
                        <span id="rsv-cart-badge" class="ml-auto bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 px-2 py-0.5 rounded-full text-xs font-medium">0 item</span>
                    </h4>
                </div>
                <div id="rsv-cart-items" class="flex-1 overflow-y-auto p-3" style="max-height: 200px;">
                    <p id="rsv-cart-empty" class="text-center text-gray-400 py-6 text-sm">Belum ada item.</p>
                </div>
                <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                    <form id="rsv-order-form" method="POST">
                        @csrf
                        <div id="rsv-hidden-items"></div>
                        <input type="hidden" name="payment_method" id="rsv-payment-method" value="cash" />
                        <input type="hidden" name="payment_status" id="rsv-payment-status" value="paid" />
                        <input type="hidden" name="status" id="rsv-order-status" value="confirmed" />
                        <textarea name="notes" rows="2" placeholder="Catatan (opsional)"
                            class="w-full px-3 py-2 mb-3 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>

                        <!-- Discount Input -->
                        <div class="bg-white dark:bg-gray-900 rounded-xl p-2.5 mb-3 border border-gray-200 dark:border-gray-700">
                            <label class="block text-xs font-semibold text-gray-500 mb-1.5">🏷️ Diskon</label>
                            <div class="flex gap-2">
                                <input type="number" id="rsv-discount-input" min="0" step="any" value="0"
                                    oninput="rsvApplyDiscount()"
                                    class="flex-1 pl-3 pr-3 py-1.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                                <div class="flex bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                                    <button type="button" id="rsv-disc-type-rp" onclick="rsvSetDiscountType('rp')"
                                        class="px-2.5 py-1.5 text-xs font-bold bg-emerald-600 text-white transition">Rp</button>
                                    <button type="button" id="rsv-disc-type-pct" onclick="rsvSetDiscountType('pct')"
                                        class="px-2.5 py-1.5 text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition">%</button>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="discount_amount" id="rsv-input-discount-amount" value="0" />

                        <!-- Price Summary -->
                        <div class="space-y-1 mb-3">
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-gray-500">Subtotal</span>
                                <span id="rsv-cart-subtotal" class="font-medium">Rp 0</span>
                            </div>
                            <div id="rsv-discount-row" class="flex justify-between items-center text-xs hidden">
                                <span class="text-red-500">Diskon</span>
                                <span id="rsv-cart-discount" class="font-medium text-red-500">- Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-1">
                                <span class="font-bold">Total</span>
                                <span id="rsv-cart-total" class="text-xl font-bold text-emerald-600">Rp 0</span>
                            </div>
                        </div>
                        <button type="button" id="rsv-btn-checkout" onclick="openRsvPaymentModal()" disabled
                            class="w-full bg-emerald-700 hover:bg-emerald-800 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-3 rounded-xl font-semibold transition text-sm">
                            Proses Pesanan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== RSV PAYMENT MODAL ==================== -->
<div id="rsv-payment-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-[60] flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8 animate-slideIn">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Pembayaran</h3>
            <button onclick="closeRsvPaymentModal()"
                class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6 space-y-2">
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span id="rsv-pay-subtotal" class="font-medium">Rp 0</span>
            </div>
            <div id="rsv-pay-discount-row" class="flex justify-between items-center text-sm hidden">
                <span class="text-red-500">Diskon</span>
                <span id="rsv-pay-discount" class="font-medium text-red-500">- Rp 0</span>
            </div>
            <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-2">
                <span class="text-gray-500 font-semibold">Total Pembayaran</span>
                <span id="rsv-pay-total" class="text-2xl font-bold text-emerald-600">Rp 0</span>
            </div>
        </div>
        <!-- Payment Method -->
        <div class="mb-5">
            <label class="block text-sm font-semibold mb-3">Metode Pembayaran</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="rsvSelectPayMethod('cash')" data-rsvmethod="cash"
                    class="rsv-pay-method-btn px-4 py-3 rounded-xl border-2 border-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 text-sm font-medium flex items-center gap-2 transition">
                    <span class="text-lg">💵</span> Cash
                </button>
                <button type="button" onclick="rsvSelectPayMethod('transfer')" data-rsvmethod="transfer"
                    class="rsv-pay-method-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">🏦</span> Transfer
                </button>
                <button type="button" onclick="rsvSelectPayMethod('qris')" data-rsvmethod="qris"
                    class="rsv-pay-method-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">📱</span> QRIS
                </button>
                <button type="button" onclick="rsvSelectPayMethod('e-wallet')" data-rsvmethod="e-wallet"
                    class="rsv-pay-method-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">💳</span> E-Wallet
                </button>
            </div>
        </div>
        <!-- Payment Status -->
        <div class="mb-5">
            <label class="block text-sm font-semibold mb-3">Status Pembayaran</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="rsvSelectPayStatus('paid')" data-rsvpaystatus="paid"
                    class="rsv-pay-status-btn px-4 py-3 rounded-xl border-2 border-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 text-sm font-medium flex items-center gap-2 transition">
                    <span class="text-lg">✅</span> Lunas
                </button>
                <button type="button" onclick="rsvSelectPayStatus('unpaid')" data-rsvpaystatus="unpaid"
                    class="rsv-pay-status-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">⏳</span> Belum Bayar
                </button>
            </div>
        </div>
        <!-- Order Status -->
        <div class="mb-6">
            <label class="block text-sm font-semibold mb-3">Status Pesanan</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="rsvSelectOrderStatus('confirmed')" data-rsvorderstatus="confirmed"
                    class="rsv-order-status-btn px-4 py-3 rounded-xl border-2 border-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 text-sm font-medium flex items-center gap-2 transition">
                    <span class="text-lg">✅</span> Dikonfirmasi
                </button>
                <button type="button" onclick="rsvSelectOrderStatus('processing')" data-rsvorderstatus="processing"
                    class="rsv-order-status-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">🔄</span> Diproses
                </button>
                <button type="button" onclick="rsvSelectOrderStatus('ready')" data-rsvorderstatus="ready"
                    class="rsv-order-status-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">📦</span> Siap Ambil
                </button>
                <button type="button" onclick="rsvSelectOrderStatus('completed')" data-rsvorderstatus="completed"
                    class="rsv-order-status-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">🎉</span> Selesai
                </button>
            </div>
        </div>
        <button type="button" onclick="rsvConfirmPayment()"
            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Konfirmasi & Simpan
        </button>
    </div>
</div>

@push('scripts')
<script>
    // ============================================
    // Route prefix detection
    // ============================================
    const currentPath = window.location.pathname;
    let routePrefix = '/admin';
    if (currentPath.startsWith('/super-admin')) {
        routePrefix = '/super-admin';
    } else if (currentPath.startsWith('/kasir')) {
        routePrefix = '/kasir';
    }

    // ============================================
    // Create Modal
    // ============================================
    function openCreateModal() {
        document.getElementById('create-modal').classList.remove('hidden');
    }

    function closeCreateModal() {
        document.getElementById('create-modal').classList.add('hidden');
    }

    // ============================================
    // Detail Modal
    // ============================================
    function viewReservation(id) {
        document.getElementById('detail-content').innerHTML = '<p class="text-center text-gray-500 py-8">Memuat...</p>';
        document.getElementById('detail-modal').classList.remove('hidden');

        fetch(routePrefix + '/reservasi/' + id)
            .then(r => r.json())
            .then(rsv => {
                const statusLabels = {
                    pending: '⏳ Pending',
                    confirmed: '✅ Dikonfirmasi',
                    cancelled: '❌ Dibatalkan',
                    completed: '🎉 Selesai'
                };
                let html = `
                            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 mb-1">Customer</p>
                                    <p class="font-semibold">${rsv.customer_name}</p>
                                    <p class="text-sm text-gray-500">${rsv.customer_phone}</p>
                                    ${rsv.customer_email ? `<p class="text-sm text-gray-500">${rsv.customer_email}</p>` : ''}
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 mb-1">Status</p>
                                    <p class="font-semibold">${statusLabels[rsv.status] || rsv.status}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 mb-1">Meja</p>
                                    <p class="font-semibold">${rsv.table ? 'Meja #' + rsv.table.table_number + ' (Kapasitas: ' + rsv.table.capacity + ')' : '-'}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 mb-1">Tanggal & Waktu</p>
                                    <p class="font-semibold">${rsv.reservation_date ? new Date(rsv.reservation_date).toLocaleDateString('id-ID', {day:'numeric',month:'long',year:'numeric'}) : '-'} ${rsv.reservation_time_slot || ''}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 mb-1">Jumlah Tamu</p>
                                    <p class="font-semibold">${rsv.guest_count} orang</p>
                                </div>
                            </div>`;

                if (rsv.special_requests) {
                    html += `<div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 mb-6"><p class="text-sm font-medium mb-1">Permintaan Khusus:</p><p class="text-sm">${rsv.special_requests}</p></div>`;
                }
                if (rsv.cancel_reason) {
                    html += `<div class="bg-red-50 dark:bg-red-900/20 rounded-xl p-4 mb-6"><p class="text-sm font-medium text-red-700 dark:text-red-300 mb-1">Alasan Pembatalan:</p><p class="text-sm text-red-600 dark:text-red-400">${rsv.cancel_reason}</p></div>`;
                }

                // Orders
                if (rsv.orders && rsv.orders.length > 0) {
                    html += `<h4 class="font-bold mb-3 text-lg">Pesanan Terkait</h4>`;
                    rsv.orders.forEach(order => {
                        const payLabel = order.payment_status === 'paid' ? '✅ Lunas' : '⏳ Belum Bayar';
                        html += `
                                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-3">
                                        <div class="flex justify-between items-center mb-2">
                                            <span class="font-mono font-bold text-sm">${order.order_number}</span>
                                            <span class="text-sm capitalize px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">${order.status}</span>
                                        </div>
                                        <div class="flex justify-between text-sm mb-2">
                                            <span class="text-gray-500">Pembayaran: ${order.payment_method || 'Cash'}</span>
                                            <span>${payLabel}</span>
                                        </div>`;
                        if (order.items && order.items.length > 0) {
                            html += `<div class="space-y-1 border-t border-gray-200 dark:border-gray-700 pt-2">`;
                            order.items.forEach(item => {
                                html += `<div class="flex justify-between text-sm"><span>${item.menu_name} x${item.quantity}</span><span>Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</span></div>`;
                            });
                            html += `</div>`;
                        }
                        if (parseFloat(order.discount_amount) > 0) {
                            html += `<div class="flex justify-between text-sm border-t border-gray-200 dark:border-gray-700 pt-2 mt-2"><span>Subtotal</span><span>Rp ${parseInt(order.subtotal).toLocaleString('id-ID')}</span></div>`;
                            html += `<div class="flex justify-between text-sm text-red-500"><span>Diskon</span><span>- Rp ${parseInt(order.discount_amount).toLocaleString('id-ID')}</span></div>`;
                        }
                        html += `<div class="flex justify-between font-bold text-sm ${parseFloat(order.discount_amount) <= 0 ? 'border-t border-gray-200 dark:border-gray-700 pt-2 mt-2' : ''}"><span>Total</span><span>Rp ${parseInt(order.total_amount).toLocaleString('id-ID')}</span></div></div>`;
                    });
                } else {
                    html += `<div class="text-center py-6 text-gray-400"><p class="text-sm">Belum ada pesanan untuk reservasi ini</p></div>`;
                }

                document.getElementById('detail-content').innerHTML = html;
            })
            .catch(() => {
                document.getElementById('detail-content').innerHTML = '<p class="text-center text-red-500 py-8">Gagal memuat data</p>';
            });
    }

    function closeDetailModal() {
        document.getElementById('detail-modal').classList.add('hidden');
    }

    // ============================================
    // Status Update Modal
    // ============================================
    function openStatusModal(id, currentStatus) {
        const form = document.getElementById('status-form');
        form.action = routePrefix + '/reservasi/' + id + '/status';
        const statusFlow = {
            'pending': 'confirmed',
            'confirmed': 'completed'
        };
        document.getElementById('rsv-status-select').value = statusFlow[currentStatus] || 'confirmed';
        document.getElementById('rsv-cancel-reason').value = '';
        toggleRsvCancelReason();
        document.getElementById('status-modal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('status-modal').classList.add('hidden');
    }

    function toggleRsvCancelReason() {
        const s = document.getElementById('rsv-status-select').value;
        const f = document.getElementById('rsv-cancel-reason-field');
        if (s === 'cancelled') {
            f.classList.remove('hidden');
            document.getElementById('rsv-cancel-reason').setAttribute('required', 'required');
        } else {
            f.classList.add('hidden');
            document.getElementById('rsv-cancel-reason').removeAttribute('required');
        }
    }

    // ============================================
    // Add Order Modal — Cart Logic
    // ============================================
    let rsvCart = [];
    let rsvCurrentCategory = 'all';
    let rsvCurrentReservationId = null;
    let rsvDiscountType = 'rp';
    const rsvAllMenus = @json($menus);

    function openOrderModal(reservationId) {
        rsvCurrentReservationId = reservationId;
        rsvCart = [];
        rsvDiscountType = 'rp';
        document.getElementById('rsv-discount-input').value = 0;
        document.getElementById('rsv-disc-type-rp').className = 'px-2.5 py-1.5 text-xs font-bold bg-emerald-600 text-white transition';
        document.getElementById('rsv-disc-type-pct').className = 'px-2.5 py-1.5 text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition';
        rsvRenderCart();
        document.getElementById('rsv-order-form').action = routePrefix + '/reservasi/' + reservationId + '/order';
        document.getElementById('order-modal').classList.remove('hidden');
    }

    function closeOrderModal() {
        document.getElementById('order-modal').classList.add('hidden');
    }

    function rsvAddToCart(menuId) {
        const menu = rsvAllMenus.find(m => m.id === menuId);
        if (!menu) return;
        const existing = rsvCart.find(c => c.id === menuId);
        if (existing) {
            if (menu.stock !== null && existing.qty >= menu.stock) return;
            existing.qty++;
        } else {
            rsvCart.push({
                id: menu.id,
                name: menu.name,
                price: parseFloat(menu.price),
                qty: 1
            });
        }
        rsvRenderCart();
    }

    function rsvUpdateQty(menuId, delta) {
        const item = rsvCart.find(c => c.id === menuId);
        if (!item) return;
        const menu = rsvAllMenus.find(m => m.id === menuId);
        item.qty += delta;
        if (item.qty <= 0) {
            rsvCart = rsvCart.filter(c => c.id !== menuId);
        } else if (menu && menu.stock !== null && item.qty > menu.stock) {
            item.qty = menu.stock;
        }
        rsvRenderCart();
    }

    function rsvRemoveItem(menuId) {
        rsvCart = rsvCart.filter(c => c.id !== menuId);
        rsvRenderCart();
    }

    function rsvRenderCart() {
        const container = document.getElementById('rsv-cart-items');
        const badge = document.getElementById('rsv-cart-badge');
        const subtotalEl = document.getElementById('rsv-cart-subtotal');
        const totalEl = document.getElementById('rsv-cart-total');
        const discountRow = document.getElementById('rsv-discount-row');
        const discountEl = document.getElementById('rsv-cart-discount');
        const hiddenContainer = document.getElementById('rsv-hidden-items');
        const submitBtn = document.getElementById('rsv-btn-checkout');

        hiddenContainer.innerHTML = '';

        if (rsvCart.length === 0) {
            container.innerHTML = '<p class="text-center text-gray-400 py-6 text-sm">Belum ada item.</p>';
            badge.textContent = '0 item';
            subtotalEl.textContent = 'Rp 0';
            totalEl.textContent = 'Rp 0';
            discountRow.classList.add('hidden');
            document.getElementById('rsv-input-discount-amount').value = 0;
            submitBtn.disabled = true;
            return;
        }

        submitBtn.disabled = false;
        let subtotal = 0;
        let totalItems = 0;
        let html = '<div class="space-y-2">';

        rsvCart.forEach((item, index) => {
            const sub = item.price * item.qty;
            subtotal += sub;
            totalItems += item.qty;

            html += `
                        <div class="flex items-center gap-2 bg-white dark:bg-gray-900 rounded-xl p-2">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-xs truncate">${item.name}</p>
                                <p class="text-xs text-emerald-600">Rp ${item.price.toLocaleString('id-ID')}</p>
                            </div>
                            <div class="flex items-center gap-1">
                                <button type="button" onclick="rsvUpdateQty(${item.id}, -1)"
                                    class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold">-</button>
                                <span class="w-5 text-center font-bold text-xs">${item.qty}</span>
                                <button type="button" onclick="rsvUpdateQty(${item.id}, 1)"
                                    class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs font-bold">+</button>
                            </div>
                            <p class="font-bold text-xs w-20 text-right">Rp ${sub.toLocaleString('id-ID')}</p>
                            <button type="button" onclick="rsvRemoveItem(${item.id})" class="text-red-400 hover:text-red-600 text-sm">&times;</button>
                        </div>`;

            hiddenContainer.innerHTML += `
                        <input type="hidden" name="items[${index}][menu_id]" value="${item.id}" />
                        <input type="hidden" name="items[${index}][quantity]" value="${item.qty}" />`;
        });

        html += '</div>';
        container.innerHTML = html;
        badge.textContent = `${totalItems} item`;
        subtotalEl.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;

        // Calculate discount
        const discInput = parseFloat(document.getElementById('rsv-discount-input').value) || 0;
        let discountAmount = 0;

        if (rsvDiscountType === 'pct') {
            discountAmount = Math.round(subtotal * Math.min(discInput, 100) / 100);
        } else {
            discountAmount = Math.min(discInput, subtotal);
        }

        if (discountAmount > 0) {
            discountRow.classList.remove('hidden');
            discountEl.textContent = `- Rp ${discountAmount.toLocaleString('id-ID')}`;
        } else {
            discountRow.classList.add('hidden');
        }

        const total = subtotal - discountAmount;
        totalEl.textContent = `Rp ${total.toLocaleString('id-ID')}`;
        document.getElementById('rsv-input-discount-amount').value = discountAmount;
    }

    // ============================================
    // Discount functions for reservation order
    // ============================================
    function rsvSetDiscountType(type) {
        rsvDiscountType = type;
        const rpBtn = document.getElementById('rsv-disc-type-rp');
        const pctBtn = document.getElementById('rsv-disc-type-pct');

        if (type === 'rp') {
            rpBtn.className = 'px-2.5 py-1.5 text-xs font-bold bg-emerald-600 text-white transition';
            pctBtn.className = 'px-2.5 py-1.5 text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition';
        } else {
            pctBtn.className = 'px-2.5 py-1.5 text-xs font-bold bg-emerald-600 text-white transition';
            rpBtn.className = 'px-2.5 py-1.5 text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 transition';
        }

        rsvApplyDiscount();
    }

    function rsvApplyDiscount() {
        rsvRenderCart();
    }

    // ============================================
    // Menu Filter
    // ============================================
    function rsvFilterMenus() {
        const query = document.getElementById('rsv-search-menu').value.toLowerCase();
        document.querySelectorAll('.rsv-menu-item').forEach(el => {
            const name = el.dataset.name.toLowerCase();
            const cat = el.dataset.category;
            const matchSearch = name.includes(query);
            const matchCat = rsvCurrentCategory === 'all' || cat === rsvCurrentCategory;
            el.style.display = (matchSearch && matchCat) ? '' : 'none';
        });
    }

    function rsvFilterByCategory(catId) {
        rsvCurrentCategory = catId;
        document.querySelectorAll('.rsv-cat-btn').forEach(btn => {
            btn.classList.remove('bg-emerald-700', 'text-white');
            btn.classList.add('bg-gray-200', 'dark:bg-gray-800');
        });
        const active = document.querySelector(`.rsv-cat-btn[data-rsvcat="${catId}"]`);
        if (active) {
            active.classList.remove('bg-gray-200', 'dark:bg-gray-800');
            active.classList.add('bg-emerald-700', 'text-white');
        }
        rsvFilterMenus();
    }

    // ============================================
    // Payment Modal for Reservation Order
    // ============================================
    let rsvSelectedMethod = 'cash';
    let rsvSelectedPayStatus = 'paid';
    let rsvSelectedOrderStatus = 'confirmed';

    function openRsvPaymentModal() {
        // Update payment modal displays
        document.getElementById('rsv-pay-subtotal').textContent = document.getElementById('rsv-cart-subtotal').textContent;
        document.getElementById('rsv-pay-total').textContent = document.getElementById('rsv-cart-total').textContent;

        const discAmt = parseFloat(document.getElementById('rsv-input-discount-amount').value) || 0;
        if (discAmt > 0) {
            document.getElementById('rsv-pay-discount-row').classList.remove('hidden');
            document.getElementById('rsv-pay-discount').textContent = document.getElementById('rsv-cart-discount').textContent;
        } else {
            document.getElementById('rsv-pay-discount-row').classList.add('hidden');
        }

        rsvSelectPayMethod('cash');
        rsvSelectPayStatus('paid');
        rsvSelectOrderStatus('confirmed');
        document.getElementById('rsv-payment-modal').classList.remove('hidden');
    }

    function closeRsvPaymentModal() {
        document.getElementById('rsv-payment-modal').classList.add('hidden');
    }

    function rsvSelectPayMethod(method) {
        rsvSelectedMethod = method;
        document.querySelectorAll('.rsv-pay-method-btn').forEach(btn => {
            const isActive = btn.dataset.rsvmethod === method;
            btn.classList.toggle('border-emerald-600', isActive);
            btn.classList.toggle('bg-emerald-50', isActive);
            btn.classList.toggle('dark:bg-emerald-900/20', isActive);
            btn.classList.toggle('border-gray-200', !isActive);
            btn.classList.toggle('dark:border-gray-700', !isActive);
        });
    }

    function rsvSelectPayStatus(status) {
        rsvSelectedPayStatus = status;
        document.querySelectorAll('.rsv-pay-status-btn').forEach(btn => {
            const isActive = btn.dataset.rsvpaystatus === status;
            btn.classList.toggle('border-emerald-600', isActive);
            btn.classList.toggle('bg-emerald-50', isActive);
            btn.classList.toggle('dark:bg-emerald-900/20', isActive);
            btn.classList.toggle('border-gray-200', !isActive);
            btn.classList.toggle('dark:border-gray-700', !isActive);
        });
    }

    function rsvSelectOrderStatus(status) {
        rsvSelectedOrderStatus = status;
        document.querySelectorAll('.rsv-order-status-btn').forEach(btn => {
            const isActive = btn.dataset.rsvorderstatus === status;
            btn.classList.toggle('border-emerald-600', isActive);
            btn.classList.toggle('bg-emerald-50', isActive);
            btn.classList.toggle('dark:bg-emerald-900/20', isActive);
            btn.classList.toggle('border-gray-200', !isActive);
            btn.classList.toggle('dark:border-gray-700', !isActive);
        });
    }

    function rsvConfirmPayment() {
        document.getElementById('rsv-payment-method').value = rsvSelectedMethod;
        document.getElementById('rsv-payment-status').value = rsvSelectedPayStatus;
        document.getElementById('rsv-order-status').value = rsvSelectedOrderStatus;
        document.getElementById('rsv-order-form').submit();
    }

    // ============================================
    // Auto-dismiss notification
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        const notification = document.getElementById('notification');
        if (notification) {
            setTimeout(() => {
                notification.style.transition = 'opacity 0.5s';
                notification.style.opacity = '0';
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }
    });
</script>
@endpush
@endsection