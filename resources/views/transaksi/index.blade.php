@extends('layouts.app')

@section('title', 'Transaksi Kasir')
@section('content')
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

<div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-8rem)]">
    <!-- LEFT: Menu Grid -->
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Header + Search -->
        <div class="flex flex-col sm:flex-row gap-3 mb-4">
            <div class="relative flex-1">
                <svg class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="search-menu" placeholder="Cari menu..."
                    oninput="filterMenus()"
                    class="w-full pl-12 pr-4 py-3 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
            </div>
            <form action="{{ route('kasir.transaksi.toggle-status') }}" method="POST" class="shrink-0 flex">
                @csrf
                <button type="submit" class="px-5 py-3 rounded-xl font-bold text-sm transition text-white flex-1 sm:flex-none {{ ($store && $store->is_active) ? 'bg-red-600 hover:bg-red-700' : 'bg-emerald-600 hover:bg-emerald-700' }}">
                    {{ ($store && $store->is_active) ? '🔴 Tutup Toko' : '🟢 Buka Toko' }}
                </button>
            </form>
        </div>

        <!-- Category Tabs -->
        <div class="flex flex-wrap gap-2 mb-4">
            <button onclick="filterByCategory('all')" data-cat="all"
                class="cat-btn px-4 py-2 rounded-full text-sm font-medium bg-emerald-700 text-white">
                Semua
            </button>
            @foreach ($categories as $category)
            <button onclick="filterByCategory('{{ $category->id }}')" data-cat="{{ $category->id }}"
                class="cat-btn px-4 py-2 rounded-full text-sm font-medium bg-gray-200 dark:bg-gray-800 hover:bg-gray-300 dark:hover:bg-gray-700">
                {{ $category->name }}
            </button>
            @endforeach
        </div>

        <!-- Menu Grid -->
        <div id="menu-grid" class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 gap-3 overflow-y-auto flex-1 pb-4">
            @foreach ($menus as $menu)
            <div class="menu-item bg-white dark:bg-gray-900 rounded-2xl p-3 border border-gray-200 dark:border-gray-800 cursor-pointer hover:shadow-lg hover:border-emerald-500 transition group"
                data-id="{{ $menu->id }}"
                data-name="{{ $menu->name }}"
                data-price="{{ $menu->price }}"
                data-category="{{ $menu->category_id }}"
                data-stock="{{ $menu->stock }}"
                onclick="addToCart({{ $menu->id }})">
                @if ($menu->image)
                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                    class="w-full h-24 object-cover rounded-xl mb-2" />
                @else
                <div class="w-full h-24 bg-gray-100 dark:bg-gray-800 rounded-xl mb-2 flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                        </path>
                    </svg>
                </div>
                @endif
                <h4 class="font-semibold text-sm line-clamp-1">{{ $menu->name }}</h4>
                <p class="text-emerald-600 dark:text-emerald-400 font-bold text-sm">
                    Rp {{ number_format($menu->price, 0, ',', '.') }}
                </p>
                @if ($menu->stock !== null)
                <p class="text-xs text-gray-400 mt-1">Stok: {{ $menu->stock }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- RIGHT: Cart Panel -->
    <div class="w-full lg:w-96 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200 dark:border-gray-800 flex flex-col overflow-hidden">
        <!-- Cart Header -->
        <div class="p-5 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-xl font-bold flex items-center gap-2">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z">
                    </path>
                </svg>
                Keranjang
                <span id="cart-badge"
                    class="ml-auto bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 px-3 py-0.5 rounded-full text-sm font-medium">
                    0 item
                </span>
            </h3>
        </div>

        <!-- Cart Items -->
        <div id="cart-items" class="flex-1 overflow-y-auto p-4">
            <p id="cart-empty" class="text-center text-gray-400 py-8 text-sm">Belum ada item.<br>Klik menu untuk menambahkan.</p>
        </div>

        <!-- Customer Info + Total -->
        <div class="border-t border-gray-200 dark:border-gray-800 p-4">
            <form id="pos-form" method="POST" action="{{ route('kasir.transaksi.store') }}">
                @csrf

                <div class="space-y-3 mb-4">
                    <input type="text" name="customer_name" required placeholder="Nama Customer *"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    <input type="tel" name="customer_phone" required placeholder="No. Telepon *"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    <textarea name="notes" rows="2" placeholder="Catatan (opsional)"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <!-- Promo Code Input -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 mb-3">
                    <label class="block text-xs font-semibold text-gray-500 mb-2">🎁 Kode Promo</label>
                    <div class="flex gap-2">
                        <input type="text" id="kasir-promo-input" autocomplete="off"
                            class="w-full pl-4 pr-4 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 uppercase"
                            placeholder="KODE PROMO" />
                        <button type="button" onclick="applyKasirPromo()"
                            class="px-3 py-2 text-xs font-bold bg-gray-800 text-white dark:bg-gray-200 dark:text-gray-900 rounded-xl transition whitespace-nowrap">
                            Gunakan
                        </button>
                    </div>
                    <p id="kasir-promo-msg" class="text-xs mt-2 hidden"></p>
                </div>

                <!-- Discount Input -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-3 mb-3">
                    <label class="block text-xs font-semibold text-gray-500 mb-2">🏷️ Diskon</label>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <input type="number" id="discount-input" min="0" step="any" value="0"
                                oninput="applyDiscount()"
                                class="w-full pl-4 pr-4 py-2 bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        </div>
                        <div class="flex bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-700 rounded-xl overflow-hidden">
                            <button type="button" id="disc-type-rp" onclick="setDiscountType('rp')"
                                class="px-3 py-2 text-xs font-bold bg-emerald-600 text-white transition">Rp</button>
                            <button type="button" id="disc-type-pct" onclick="setDiscountType('pct')"
                                class="px-3 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">%</button>
                        </div>
                    </div>
                </div>

                <div id="hidden-items-container"></div>
                <input type="hidden" name="discount_code" id="input-discount-code" value="">
                <input type="hidden" name="discount_amount" id="input-discount-amount" value="0" />
                <input type="hidden" name="payment_method" id="input-payment-method" value="cash" />
                <input type="hidden" name="payment_status" id="input-payment-status" value="paid" />
                <input type="hidden" name="status" id="input-order-status" value="confirmed" />
                <div id="pos-tax-rate" data-rate="{{ $activeTax ? $activeTax->rate : 0 }}" style="display:none"></div>

                <!-- Price Summary -->
                <div class="space-y-1 mb-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Subtotal</span>
                        <span id="cart-subtotal" class="font-medium">Rp 0</span>
                    </div>
                    <div id="discount-row" class="flex justify-between items-center text-sm hidden">
                        <span class="text-red-500">Diskon</span>
                        <span id="cart-discount" class="font-medium text-red-500">- Rp 0</span>
                    </div>
                    <div id="tax-row" class="flex justify-between items-center text-sm hidden">
                        <span class="text-gray-500">Pajak ({{ $activeTax ? $activeTax->rate + 0 : 0 }}%)</span>
                        <span id="cart-tax" class="font-medium text-gray-500">+ Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-2">
                        <span class="text-lg font-bold">Total</span>
                        <span id="cart-total" class="text-2xl font-bold text-emerald-600">Rp 0</span>
                    </div>
                </div>

                <button type="button" id="btn-submit" onclick="openPaymentModal()"
                    class="w-full bg-emerald-700 hover:bg-emerald-800 disabled:bg-gray-400 disabled:cursor-not-allowed text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2"
                    disabled>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                    Proses Transaksi
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8 animate-slideIn">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold">Pembayaran</h3>
            <button onclick="closePaymentModal()"
                class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Payment Summary -->
        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6 space-y-2">
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-500">Subtotal</span>
                <span id="payment-subtotal" class="font-medium">Rp 0</span>
            </div>
            <div id="payment-discount-row" class="flex justify-between items-center text-sm hidden">
                <span class="text-red-500">Diskon</span>
                <span id="payment-discount" class="font-medium text-red-500">- Rp 0</span>
            </div>
            <div id="payment-tax-row" class="flex justify-between items-center text-sm hidden">
                <span class="text-gray-500">Pajak ({{ $activeTax ? $activeTax->rate + 0 : 0 }}%)</span>
                <span id="payment-tax" class="font-medium text-gray-500">+ Rp 0</span>
            </div>
            <div class="flex justify-between items-center border-t border-gray-200 dark:border-gray-700 pt-2">
                <span class="text-gray-500 font-semibold">Total Pembayaran</span>
                <span id="payment-total" class="text-2xl font-bold text-emerald-600">Rp 0</span>
            </div>
        </div>

        <!-- Payment Method -->
        <div class="mb-5">
            <label class="block text-sm font-semibold mb-3">Metode Pembayaran</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="selectPaymentMethod('cash')" data-method="cash"
                    class="pay-method-btn px-4 py-3 rounded-xl border-2 border-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 text-sm font-medium flex items-center gap-2 transition">
                    <span class="text-lg">💵</span> Cash
                </button>
                <button type="button" onclick="selectPaymentMethod('transfer')" data-method="transfer"
                    class="pay-method-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">🏦</span> Transfer
                </button>
                <button type="button" onclick="selectPaymentMethod('qris')" data-method="qris"
                    class="pay-method-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">📱</span> QRIS
                </button>
                <button type="button" onclick="selectPaymentMethod('e-wallet')" data-method="e-wallet"
                    class="pay-method-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">💳</span> E-Wallet
                </button>
                <button type="button" onclick="selectPaymentMethod('midtrans')" data-method="midtrans"
                    class="pay-method-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition col-span-2">
                    <span class="text-lg">🌐</span> Midtrans (Online/QRIS)
                </button>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="mb-5" id="payment-status-section">
            <label class="block text-sm font-semibold mb-3">Status Pembayaran</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="selectPaymentStatus('paid')" data-pay-status="paid"
                    class="pay-status-btn px-4 py-3 rounded-xl border-2 border-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 text-sm font-medium flex items-center gap-2 transition">
                    <span class="text-lg">✅</span> Lunas
                </button>
                <button type="button" onclick="selectPaymentStatus('unpaid')" data-pay-status="unpaid"
                    class="pay-status-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">⏳</span> Belum Bayar
                </button>
            </div>
        </div>
        <!-- Midtrans info banner (shown only when Midtrans is selected) -->
        <div id="midtrans-info-banner" class="hidden mb-5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-3">
            <p class="text-sm text-blue-700 dark:text-blue-300 flex items-center gap-2">
                <span class="text-lg">🌐</span>
                <span>Status pembayaran akan diperbarui <strong>otomatis</strong> setelah pelanggan menyelesaikan pembayaran via Midtrans.</span>
            </p>
        </div>

        <!-- Order Status -->
        <div class="mb-6">
            <label class="block text-sm font-semibold mb-3">Status Pesanan</label>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="selectOrderStatus('confirmed')" data-order-status="confirmed"
                    class="order-status-btn px-4 py-3 rounded-xl border-2 border-emerald-600 bg-emerald-50 dark:bg-emerald-900/20 text-sm font-medium flex items-center gap-2 transition">
                    <span class="text-lg">✅</span> Dikonfirmasi
                </button>
                <button type="button" onclick="selectOrderStatus('processing')" data-order-status="processing"
                    class="order-status-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">🔄</span> Diproses
                </button>
                <button type="button" onclick="selectOrderStatus('ready')" data-order-status="ready"
                    class="order-status-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">📦</span> Siap Ambil
                </button>
                <button type="button" onclick="selectOrderStatus('completed')" data-order-status="completed"
                    class="order-status-btn px-4 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 text-sm font-medium flex items-center gap-2 hover:border-emerald-500 transition">
                    <span class="text-lg">🎉</span> Selesai
                </button>
            </div>
        </div>

        <!-- Confirm Button -->
        <button type="button" onclick="confirmPayment()"
            class="w-full bg-emerald-700 hover:bg-emerald-800 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            Konfirmasi & Simpan
        </button>
    </div>
</div>

<!-- Receipt Modal -->
@if (session('receipt'))
@php $receipt = session('receipt'); @endphp
<div id="receipt-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8 animate-slideIn">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-xl font-bold mb-1">Transaksi Berhasil!</h3>
            <p class="text-sm text-gray-500">{{ $receipt['order_number'] }}</p>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-4 space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Customer</span>
                <span class="font-medium">{{ $receipt['customer_name'] }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Pembayaran</span>
                <span class="font-medium capitalize">{{ $receipt['payment_method'] ?? 'Cash' }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Status Bayar</span>
                <span class="font-medium">{{ $receipt['payment_status'] === 'paid' ? '✅ Lunas' : '⏳ Belum Bayar' }}</span>
            </div>
            @php
            $statusLabels = ['confirmed' => '✅ Dikonfirmasi', 'processing' => '🔄 Diproses', 'ready' => '📦 Siap Ambil', 'completed' => '🎉 Selesai'];
            @endphp
            <div class="flex justify-between text-sm">
                <span class="text-gray-500">Status Pesanan</span>
                <span class="font-medium">{{ $statusLabels[$receipt['status']] ?? ucfirst($receipt['status']) }}</span>
            </div>
            <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2"></div>
            @foreach ($receipt['items'] as $item)
            <div class="flex justify-between text-sm">
                <span>{{ $item['menu_name'] }} x{{ $item['quantity'] }}</span>
                <span>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
            </div>
            @endforeach
            @if ($receipt['discount_amount'] > 0)
            <div class="flex justify-between text-sm border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                <span>Subtotal</span>
                <span>Rp {{ number_format($receipt['subtotal'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm text-red-500">
                <span>Diskon</span>
                <span>- Rp {{ number_format($receipt['discount_amount'], 0, ',', '.') }}</span>
            </div>
            @endif
            @if (isset($receipt['tax_amount']) && $receipt['tax_amount'] > 0)
            <div class="flex justify-between text-sm text-gray-500">
                <span>Pajak</span>
                <span>+ Rp {{ number_format($receipt['tax_amount'], 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between font-bold text-sm {{ $receipt['discount_amount'] <= 0 ? 'border-t border-gray-200 dark:border-gray-700 pt-2 mt-2' : '' }}">
                <span>Total</span>
                <span>Rp {{ number_format($receipt['total_amount'], 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('kasir.transaksi.print', $receipt['id']) }}" target="_blank"
                class="flex-1 bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-800 dark:text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                </svg>
                Cetak Struk
            </a>
            <button onclick="document.getElementById('receipt-modal').classList.add('hidden')"
                class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white py-3 rounded-xl font-semibold transition">
                Transaksi Baru
            </button>
        </div>
    </div>
</div>
@endif

@push('scripts')
@if(session('snap_token'))
{{-- Store midtrans data in HTML attributes to avoid Blade-in-JS formatter issues --}}
<div id="midtrans-meta"
    data-snap-token="{{ session('snap_token') }}"
    data-order-id="{{ session('midtrans_order_id', 0) }}"
    data-callback-url="{{ route('kasir.transaksi.midtrans-success', session('midtrans_order_id', 0)) }}"
    style="display:none"></div>
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var meta = document.getElementById('midtrans-meta');
        var snapToken = meta.dataset.snapToken;
        var callbackUrl = meta.dataset.callbackUrl;

        window.snap.pay(snapToken, {
            onSuccess: function(result) {
                console.log('Payment success', result);
                fetch(callbackUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({
                            result: result
                        })
                    })
                    .then(function(res) {
                        return res.json();
                    })
                    .then(function(data) {
                        window.location.reload();
                    })
                    .catch(function() {
                        alert('Pembayaran berhasil tetapi gagal memperbarui status. Silakan refresh halaman.');
                        window.location.reload();
                    });
            },
            onPending: function(result) {
                console.log('Payment pending', result);
                alert('Pembayaran sedang diproses. Status akan diperbarui otomatis.');
            },
            onError: function(result) {
                console.log('Payment error', result);
                alert('Pembayaran gagal! Silakan coba lagi.');
            },
            onClose: function() {
                console.log('Payment popup closed');
            }
        });
    });
</script>
@endif

<script>
    // ============================================
    // Cart state
    // ============================================
    let cart = [];
    let currentCategory = 'all';
    let discountType = 'rp'; // 'rp' or 'pct'

    // ============================================
    // Menu data from server
    // ============================================
    const allMenus = @json($menus);
    const activeTaxRate = document.getElementById('pos-tax-rate').dataset.rate * 1;

    // ============================================
    // Add to cart
    // ============================================
    function addToCart(menuId) {
        const menu = allMenus.find(m => m.id === menuId);
        if (!menu) return;

        const existing = cart.find(c => c.id === menuId);
        if (existing) {
            // Check stock
            if (menu.stock !== null && existing.qty >= menu.stock) {
                return;
            }
            existing.qty++;
        } else {
            cart.push({
                id: menu.id,
                name: menu.name,
                price: parseFloat(menu.price),
                qty: 1,
            });
        }
        renderCart();
    }

    function updateQty(menuId, delta) {
        const item = cart.find(c => c.id === menuId);
        if (!item) return;

        const menu = allMenus.find(m => m.id === menuId);
        item.qty += delta;

        if (item.qty <= 0) {
            cart = cart.filter(c => c.id !== menuId);
        } else if (menu && menu.stock !== null && item.qty > menu.stock) {
            item.qty = menu.stock;
        }

        renderCart();
    }

    function removeItem(menuId) {
        cart = cart.filter(c => c.id !== menuId);
        renderCart();
    }

    // ============================================
    // Render cart
    // ============================================
    function renderCart() {
        const container = document.getElementById('cart-items');
        const badge = document.getElementById('cart-badge');
        const subtotalEl = document.getElementById('cart-subtotal');
        const totalEl = document.getElementById('cart-total');
        const discountRow = document.getElementById('discount-row');
        const discountEl = document.getElementById('cart-discount');
        const taxRow = document.getElementById('tax-row');
        const taxEl = document.getElementById('cart-tax');
        const hiddenContainer = document.getElementById('hidden-items-container');
        const submitBtn = document.getElementById('btn-submit');

        hiddenContainer.innerHTML = '';

        if (cart.length === 0) {
            container.innerHTML = '<p id="cart-empty" class="text-center text-gray-400 py-8 text-sm">Belum ada item.<br>Klik menu untuk menambahkan.</p>';
            badge.textContent = '0 item';
            subtotalEl.textContent = 'Rp 0';
            totalEl.textContent = 'Rp 0';
            discountRow.classList.add('hidden');
            taxRow.classList.add('hidden');
            document.getElementById('input-discount-amount').value = 0;
            submitBtn.disabled = true;
            return;
        }

        submitBtn.disabled = false;
        let subtotal = 0;
        let totalItems = 0;
        let html = '<div class="space-y-2">';

        cart.forEach((item, index) => {
            const sub = item.price * item.qty;
            subtotal += sub;
            totalItems += item.qty;

            html += `
                        <div class="flex items-center gap-3 bg-gray-50 dark:bg-gray-800 rounded-xl p-3">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-sm truncate">${item.name}</p>
                                <p class="text-xs text-emerald-600">Rp ${item.price.toLocaleString('id-ID')}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="updateQty(${item.id}, -1)"
                                    class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-red-100 dark:hover:bg-red-900 flex items-center justify-center text-sm font-bold">-</button>
                                <span class="w-6 text-center font-bold text-sm">${item.qty}</span>
                                <button type="button" onclick="updateQty(${item.id}, 1)"
                                    class="w-7 h-7 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-emerald-100 dark:hover:bg-emerald-900 flex items-center justify-center text-sm font-bold">+</button>
                            </div>
                            <p class="font-bold text-sm w-24 text-right">Rp ${sub.toLocaleString('id-ID')}</p>
                            <button type="button" onclick="removeItem(${item.id})"
                                class="text-red-400 hover:text-red-600 text-lg">&times;</button>
                        </div>`;

            // Hidden inputs
            hiddenContainer.innerHTML += `
                        <input type="hidden" name="items[${index}][menu_id]" value="${item.id}" />
                        <input type="hidden" name="items[${index}][quantity]" value="${item.qty}" />`;
        });

        html += '</div>';
        container.innerHTML = html;
        badge.textContent = `${totalItems} item`;
        subtotalEl.textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;

        // Calculate discount
        const discInput = parseFloat(document.getElementById('discount-input').value) || 0;
        let discountAmount = 0;

        if (discountType === 'pct') {
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

        const taxAmount = Math.round((subtotal - discountAmount) * (activeTaxRate / 100));

        if (taxAmount > 0) {
            taxRow.classList.remove('hidden');
            taxEl.textContent = `+ Rp ${taxAmount.toLocaleString('id-ID')}`;
        } else {
            taxRow.classList.add('hidden');
        }

        const total = subtotal - discountAmount + taxAmount;
        totalEl.textContent = `Rp ${total.toLocaleString('id-ID')}`;
        document.getElementById('input-discount-amount').value = discountAmount;
    }

    // ============================================
    // Discount functions
    // ============================================
    function setDiscountType(type) {
        discountType = type;
        const rpBtn = document.getElementById('disc-type-rp');
        const pctBtn = document.getElementById('disc-type-pct');

        if (type === 'rp') {
            rpBtn.className = 'px-3 py-2 text-xs font-bold bg-emerald-600 text-white transition';
            pctBtn.className = 'px-3 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition';
        } else {
            pctBtn.className = 'px-3 py-2 text-xs font-bold bg-emerald-600 text-white transition';
            rpBtn.className = 'px-3 py-2 text-xs font-bold text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition';
        }

        applyDiscount();
    }

    function applyDiscount() {
        renderCart();
    }

    // ============================================
    // Filter menus
    // ============================================
    function filterMenus() {
        const query = document.getElementById('search-menu').value.toLowerCase();
        document.querySelectorAll('.menu-item').forEach(el => {
            const name = el.dataset.name.toLowerCase();
            const cat = el.dataset.category;
            const matchSearch = name.includes(query);
            const matchCat = currentCategory === 'all' || cat === currentCategory;
            el.style.display = (matchSearch && matchCat) ? '' : 'none';
        });
    }

    function filterByCategory(catId) {
        currentCategory = catId;
        document.querySelectorAll('.cat-btn').forEach(btn => {
            btn.classList.remove('bg-emerald-700', 'text-white');
            btn.classList.add('bg-gray-200', 'dark:bg-gray-800');
        });
        const active = document.querySelector(`.cat-btn[data-cat="${catId}"]`);
        if (active) {
            active.classList.remove('bg-gray-200', 'dark:bg-gray-800');
            active.classList.add('bg-emerald-700', 'text-white');
        }
        filterMenus();
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

    // ============================================
    // Payment Modal
    // ============================================
    let selectedMethod = 'cash';
    let selectedPayStatus = 'paid';
    let selectedOrderStatus = 'confirmed';

    function openPaymentModal() {
        // Validate form first
        const form = document.getElementById('pos-form');
        const nameInput = form.querySelector('input[name="customer_name"]');
        const phoneInput = form.querySelector('input[name="customer_phone"]');

        if (!nameInput.value.trim() || !phoneInput.value.trim()) {
            nameInput.reportValidity();
            phoneInput.reportValidity();
            return;
        }

        // Update payment modal displays
        document.getElementById('payment-subtotal').textContent = document.getElementById('cart-subtotal').textContent;
        document.getElementById('payment-total').textContent = document.getElementById('cart-total').textContent;

        const discAmt = parseFloat(document.getElementById('input-discount-amount').value) || 0;
        if (discAmt > 0) {
            document.getElementById('payment-discount-row').classList.remove('hidden');
            document.getElementById('payment-discount').textContent = document.getElementById('cart-discount').textContent;
        } else {
            document.getElementById('payment-discount-row').classList.add('hidden');
        }

        const taxRow = document.getElementById('tax-row');
        if (!taxRow.classList.contains('hidden')) {
            document.getElementById('payment-tax-row').classList.remove('hidden');
            document.getElementById('payment-tax').textContent = document.getElementById('cart-tax').textContent;
        } else {
            document.getElementById('payment-tax-row').classList.add('hidden');
        }

        // Reset selections
        selectPaymentMethod('cash');
        selectPaymentStatus('paid');
        selectOrderStatus('confirmed');

        document.getElementById('payment-modal').classList.remove('hidden');
    }

    function closePaymentModal() {
        document.getElementById('payment-modal').classList.add('hidden');
    }

    function selectPaymentMethod(method) {
        selectedMethod = method;
        document.querySelectorAll('.pay-method-btn').forEach(btn => {
            const isActive = btn.dataset.method === method;
            btn.classList.toggle('border-emerald-600', isActive);
            btn.classList.toggle('bg-emerald-50', isActive);
            btn.classList.toggle('dark:bg-emerald-900/20', isActive);
            btn.classList.toggle('border-gray-200', !isActive);
            btn.classList.toggle('dark:border-gray-700', !isActive);
        });

        // Hide payment status section for Midtrans (auto-managed)
        const payStatusSection = document.getElementById('payment-status-section');
        const midtransBanner = document.getElementById('midtrans-info-banner');
        if (method === 'midtrans') {
            payStatusSection.classList.add('hidden');
            midtransBanner.classList.remove('hidden');
            // Force unpaid since Midtrans will update it
            selectPaymentStatus('unpaid');
        } else {
            payStatusSection.classList.remove('hidden');
            midtransBanner.classList.add('hidden');
        }
    }

    function selectPaymentStatus(status) {
        selectedPayStatus = status;
        document.querySelectorAll('.pay-status-btn').forEach(btn => {
            const isActive = btn.dataset.payStatus === status;
            btn.classList.toggle('border-emerald-600', isActive);
            btn.classList.toggle('bg-emerald-50', isActive);
            btn.classList.toggle('dark:bg-emerald-900/20', isActive);
            btn.classList.toggle('border-gray-200', !isActive);
            btn.classList.toggle('dark:border-gray-700', !isActive);
        });
    }

    function selectOrderStatus(status) {
        selectedOrderStatus = status;
        document.querySelectorAll('.order-status-btn').forEach(btn => {
            const isActive = btn.dataset.orderStatus === status;
            btn.classList.toggle('border-emerald-600', isActive);
            btn.classList.toggle('bg-emerald-50', isActive);
            btn.classList.toggle('dark:bg-emerald-900/20', isActive);
            btn.classList.toggle('border-gray-200', !isActive);
            btn.classList.toggle('dark:border-gray-700', !isActive);
        });
    }

    function confirmPayment() {
        document.getElementById('input-payment-method').value = selectedMethod;
        document.getElementById('input-payment-status').value = selectedPayStatus;
        document.getElementById('input-order-status').value = selectedOrderStatus;
        document.getElementById('pos-form').submit();
    }

    // ============================================
    // Promo Code Validation
    // ============================================
    async function applyKasirPromo() {
        const input = document.getElementById('kasir-promo-input');
        const msgEl = document.getElementById('kasir-promo-msg');
        const codeEl = document.getElementById('input-discount-code');
        const discInput = document.getElementById('discount-input');

        const code = input.value.trim();
        if (!code) return;

        msgEl.classList.remove('hidden');
        msgEl.className = 'text-xs mt-2 text-gray-500';
        msgEl.textContent = 'Memvalidasi...';

        // Recalculate subtotal first
        let subtotal = 0;
        cart.forEach(item => subtotal += item.price * item.qty);

        if (subtotal <= 0) {
            msgEl.className = 'text-xs mt-2 text-red-500';
            msgEl.textContent = 'Keranjang masih kosong.';
            return;
        }

        try {
            const response = await fetch('{{ route("api.discount.validate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    code: code.toUpperCase(),
                    subtotal
                })
            });
            const data = await response.json();

            if (data.error) {
                msgEl.className = 'text-xs mt-2 text-red-500';
                msgEl.textContent = data.error;
                codeEl.value = '';
            } else if (data.success) {
                msgEl.className = 'text-xs mt-2 text-emerald-500';
                msgEl.textContent = data.message;
                codeEl.value = data.discount.code;

                // Set manual discount input to match calculated
                setDiscountType('rp');
                discInput.value = data.discount.calculated_discount;
                applyDiscount();
            }
        } catch (error) {
            msgEl.className = 'text-xs mt-2 text-red-500';
            msgEl.textContent = 'Terjadi kesalahan jaringan.';
        }
    }
</script>
@endpush
@endsection