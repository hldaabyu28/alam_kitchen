@extends('layouts.app')

@section('title', 'Kelola Pesanan')
@section('content')
    <!-- ORDER MANAGEMENT CONTENT -->

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl sm:text-4xl font-bold mb-2">Kelola Pesanan</h2>
            <p class="text-gray-500 dark:text-gray-400">Pantau dan kelola semua pesanan</p>
        </div>
    </div>

    <!-- Success Notification -->
    @if (session('success'))
        <div id="notification"
            class="fixed top-24 right-4 bg-emerald-600 text-white px-6 py-4 rounded-2xl shadow-lg z-50 flex items-center gap-3 animate-slideIn">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <p>{{ session('success') }}</p>
        </div>
    @endif

    <!-- Filter Tabs -->
    @php
        $statuses = [
            'all' => 'Semua',
            'pending' => 'Pending',
            'confirmed' => 'Dikonfirmasi',
            'processing' => 'Diproses',
            'ready' => 'Siap',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
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
                    ({{ $orders->count() }})
                @endif
            </a>
        @endforeach
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold">Order #</th>
                        <th class="px-6 py-4 text-sm font-semibold">Customer</th>
                        <th class="px-6 py-4 text-sm font-semibold">Items</th>
                        <th class="px-6 py-4 text-sm font-semibold">Total</th>
                        <th class="px-6 py-4 text-sm font-semibold">Status</th>
                        <th class="px-6 py-4 text-sm font-semibold">Pembayaran</th>
                        <th class="px-6 py-4 text-sm font-semibold">Waktu</th>
                        <th class="px-6 py-4 text-sm font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($orders as $order)
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                'confirmed' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                'processing' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
                                'ready' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
                                'completed' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                            ];
                            $statusLabels = [
                                'pending' => 'Pending',
                                'confirmed' => 'Dikonfirmasi',
                                'processing' => 'Diproses',
                                'ready' => 'Siap Diambil',
                                'completed' => 'Selesai',
                                'cancelled' => 'Dibatalkan',
                            ];
                            $paymentColors = [
                                'unpaid' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300',
                                'paid' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
                                'failed' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                'expired' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                'refunded' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-6 py-4">
                                <p class="font-mono font-bold text-sm">{{ $order->order_number }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold">{{ $order->customer_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $order->items_count }} item
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                                @if ($order->discount_amount > 0)
                                    <p class="text-xs text-red-500">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? '' }}">
                                    {{ $statusLabels[$order->status] ?? $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-3 py-1 rounded-full text-xs font-medium {{ $paymentColors[$order->payment_status] ?? '' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p>{{ $order->created_at->format('d M Y') }}</p>
                                    <p class="text-gray-400">{{ $order->created_at->format('H:i') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end gap-2">
                                    <button onclick="viewOrder({{ $order->id }})"
                                        class="px-4 py-2 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded-xl text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-800 transition">
                                        Detail
                                    </button>
                                    @if (!in_array($order->status, ['completed', 'cancelled']))
                                        <button onclick="openStatusModal({{ $order->id }}, '{{ $order->status }}')"
                                            class="px-4 py-2 bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 rounded-xl text-sm font-medium hover:bg-emerald-200 dark:hover:bg-emerald-800 transition">
                                            Update
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
                                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold mb-2">Belum ada pesanan</h3>
                                <p class="text-gray-500">Pesanan akan muncul di sini setelah customer melakukan order</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Detail Modal -->
    <div id="detail-modal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-2xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto animate-slideIn">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold">Detail Pesanan</h3>
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

    <!-- Status Update Modal -->
    <div id="status-modal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8 animate-slideIn">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold">Update Status Pesanan</h3>
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
                        <select name="status" id="status-select" required onchange="toggleCancelReason()"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="confirmed">✅ Dikonfirmasi</option>
                            <option value="processing">🔄 Diproses</option>
                            <option value="ready">📦 Siap Diambil</option>
                            <option value="completed">🎉 Selesai</option>
                            <option value="cancelled">❌ Dibatalkan</option>
                        </select>
                    </div>

                    <div id="cancel-reason-field" class="hidden">
                        <label class="block text-sm font-medium mb-2">Alasan Pembatalan *</label>
                        <textarea name="cancel_reason" id="cancel-reason" rows="3"
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
            // Detail Modal
            // ============================================
            function viewOrder(orderId) {
                document.getElementById('detail-content').innerHTML = '<p class="text-center text-gray-500 py-8">Memuat...</p>';
                document.getElementById('detail-modal').classList.remove('hidden');

                fetch(routePrefix + '/orders/' + orderId)
                    .then(response => response.json())
                    .then(order => {
                        let html = `
                            <div class="grid sm:grid-cols-2 gap-4 mb-6">
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 mb-1">Order Number</p>
                                    <p class="font-mono font-bold">${order.order_number}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 mb-1">Status</p>
                                    <p class="font-semibold capitalize">${order.status}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 mb-1">Customer</p>
                                    <p class="font-semibold">${order.customer_name}</p>
                                    <p class="text-sm text-gray-500">${order.customer_email}</p>
                                    <p class="text-sm text-gray-500">${order.customer_phone}</p>
                                </div>
                                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                                    <p class="text-xs text-gray-500 mb-1">Waktu Pengambilan</p>
                                    <p class="font-semibold">${new Date(order.pickup_time).toLocaleString('id-ID')}</p>
                                </div>
                            </div>

                            ${order.notes ? `<div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 mb-6"><p class="text-sm font-medium mb-1">Catatan:</p><p class="text-sm">${order.notes}</p></div>` : ''}

                            <h4 class="font-bold mb-3">Item Pesanan</h4>
                            <div class="space-y-2 mb-6">`;

                        order.items.forEach(item => {
                            html += `
                                <div class="flex justify-between items-center bg-gray-50 dark:bg-gray-800 rounded-xl p-3">
                                    <div>
                                        <p class="font-semibold text-sm">${item.menu_name}</p>
                                        <p class="text-xs text-gray-500">Rp ${parseInt(item.unit_price).toLocaleString('id-ID')} x ${item.quantity}</p>
                                    </div>
                                    <p class="font-bold text-sm">Rp ${parseInt(item.subtotal).toLocaleString('id-ID')}</p>
                                </div>`;
                        });

                        html += `</div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span>Subtotal</span>
                                    <span>Rp ${parseInt(order.subtotal).toLocaleString('id-ID')}</span>
                                </div>`;

                        if (parseFloat(order.discount_amount) > 0) {
                            html += `<div class="flex justify-between text-sm text-red-500">
                                <span>Diskon</span>
                                <span>-Rp ${parseInt(order.discount_amount).toLocaleString('id-ID')}</span>
                            </div>`;
                        }

                        html += `<div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200 dark:border-gray-700">
                                <span>Total</span>
                                <span>Rp ${parseInt(order.total_amount).toLocaleString('id-ID')}</span>
                            </div>
                        </div>`;

                        if (order.cancel_reason) {
                            html += `<div class="mt-4 bg-red-50 dark:bg-red-900/20 rounded-xl p-4">
                                <p class="text-sm font-medium text-red-700 dark:text-red-300 mb-1">Alasan Pembatalan:</p>
                                <p class="text-sm text-red-600 dark:text-red-400">${order.cancel_reason}</p>
                            </div>`;
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
            function openStatusModal(orderId, currentStatus) {
                const form = document.getElementById('status-form');
                form.action = routePrefix + '/orders/' + orderId + '/status';

                // Set the next logical status as default
                const statusFlow = {
                    'pending': 'confirmed',
                    'confirmed': 'processing',
                    'processing': 'ready',
                    'ready': 'completed'
                };
                document.getElementById('status-select').value = statusFlow[currentStatus] || 'confirmed';
                document.getElementById('cancel-reason').value = '';
                toggleCancelReason();

                document.getElementById('status-modal').classList.remove('hidden');
            }

            function closeStatusModal() {
                document.getElementById('status-modal').classList.add('hidden');
            }

            function toggleCancelReason() {
                const status = document.getElementById('status-select').value;
                const cancelField = document.getElementById('cancel-reason-field');
                if (status === 'cancelled') {
                    cancelField.classList.remove('hidden');
                    document.getElementById('cancel-reason').setAttribute('required', 'required');
                } else {
                    cancelField.classList.add('hidden');
                    document.getElementById('cancel-reason').removeAttribute('required');
                }
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
