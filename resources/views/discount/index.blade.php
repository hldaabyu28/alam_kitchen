@extends('layouts.app')

@section('title', 'Kelola Diskon')
@section('content')
    <!-- DISCOUNT MANAGEMENT CONTENT -->

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl sm:text-4xl font-bold mb-2">Kelola Diskon</h2>
            <p class="text-gray-500 dark:text-gray-400">Kelola promo dan diskon dengan mudah</p>
        </div>

        @php
            $user = Auth::user();
            $canManage = $user && ($user->hasRole('super_admin') || $user->hasRole('admin'));
        @endphp

        @if ($canManage)
            <div class="flex gap-3">
                <button onclick="openAddModal()"
                    class="bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-xl font-medium shadow-lg hover:shadow-xl transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Diskon
                </button>
            </div>
        @endif
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

    <!-- Table -->
    <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-lg border border-gray-200 dark:border-gray-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-6 py-4 text-sm font-semibold">Nama</th>
                        <th class="px-6 py-4 text-sm font-semibold">Kode</th>
                        <th class="px-6 py-4 text-sm font-semibold">Tipe</th>
                        <th class="px-6 py-4 text-sm font-semibold">Nilai</th>
                        <th class="px-6 py-4 text-sm font-semibold">Periode</th>
                        <th class="px-6 py-4 text-sm font-semibold">Penggunaan</th>
                        <th class="px-6 py-4 text-sm font-semibold">Status</th>
                        @if ($canManage)
                            <th class="px-6 py-4 text-sm font-semibold text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($discounts as $discount)
                        @php
                            $now = now();
                            $isExpired = $discount->valid_until < $now;
                            $isUpcoming = $discount->valid_from > $now;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($discount->banner_image)
                                        <img src="{{ asset('storage/' . $discount->banner_image) }}"
                                            alt="{{ $discount->name }}" class="w-12 h-12 rounded-xl object-cover">
                                    @endif
                                    <div>
                                        <p class="font-semibold">{{ $discount->name }}</p>
                                        <p class="text-sm text-gray-500 line-clamp-1">{{ $discount->description ?? '' }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-lg text-xs font-mono font-bold bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                                    {{ $discount->code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($discount->type === 'percentage')
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300">
                                        Persentase
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                        Nominal
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if ($discount->type === 'percentage')
                                    <p class="font-semibold">{{ $discount->percentage }}%</p>
                                    @if ($discount->max_discount_amount)
                                        <p class="text-xs text-gray-500">Maks Rp
                                            {{ number_format($discount->max_discount_amount, 0, ',', '.') }}</p>
                                    @endif
                                @else
                                    <p class="font-semibold">Rp {{ number_format($discount->amount, 0, ',', '.') }}</p>
                                @endif
                                @if ($discount->min_order_amount)
                                    <p class="text-xs text-gray-400">Min Rp
                                        {{ number_format($discount->min_order_amount, 0, ',', '.') }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p>{{ $discount->valid_from->format('d M Y') }}</p>
                                    <p class="text-gray-400">s/d</p>
                                    <p>{{ $discount->valid_until->format('d M Y') }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium">{{ $discount->used_count }}
                                    / {{ $discount->usage_limit ?? '∞' }}</p>
                                <p class="text-xs text-gray-500">{{ $discount->usage_per_user }}x/user</p>
                            </td>
                            <td class="px-6 py-4">
                                @if (!$discount->is_active)
                                    <span
                                        class="px-3 py-1 bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400 rounded-full text-xs font-medium">
                                        Non-aktif
                                    </span>
                                @elseif ($isExpired)
                                    <span
                                        class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded-full text-xs font-medium">
                                        Kadaluarsa
                                    </span>
                                @elseif ($isUpcoming)
                                    <span
                                        class="px-3 py-1 bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-300 rounded-full text-xs font-medium">
                                        Akan Datang
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded-full text-xs font-medium">
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            @if ($canManage)
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick='openEditModal(@json($discount))'
                                            class="px-4 py-2 bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 rounded-xl text-sm font-medium hover:bg-emerald-200 dark:hover:bg-emerald-800 transition">
                                            Edit
                                        </button>
                                        <button onclick="openDeleteModal({{ $discount->id }})"
                                            class="px-4 py-2 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded-xl text-sm font-medium hover:bg-red-200 dark:hover:bg-red-800 transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 8 : 7 }}" class="px-6 py-12 text-center">
                                <div
                                    class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold mb-2">Belum ada diskon</h3>
                                <p class="text-gray-500 mb-4">Mulai tambahkan promo pertama Anda</p>
                                @if ($canManage)
                                    <button onclick="openAddModal()"
                                        class="bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-2 rounded-xl font-medium transition">
                                        Tambah Diskon
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($canManage)
        <!-- Add/Edit Modal -->
        <div id="discount-modal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-gray-900 rounded-3xl max-w-3xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto animate-slideIn">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="modal-title" class="text-2xl font-bold">Tambah Diskon Baru</h3>
                    <button onclick="closeModal()"
                        class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form id="discount-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST" />

                    <div class="grid sm:grid-cols-2 gap-4">
                        <!-- Name -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2">Nama Diskon *</label>
                            <input type="text" name="name" id="discount-name" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="contoh: Promo Ramadan" />
                        </div>

                        <!-- Code -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Kode Diskon *</label>
                            <input type="text" name="code" id="discount-code" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 uppercase"
                                placeholder="PROMO2026" />
                        </div>

                        <!-- Type -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Tipe Diskon *</label>
                            <select name="type" id="discount-type" required onchange="toggleDiscountFields()"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                                <option value="percentage">Persentase (%)</option>
                                <option value="fixed">Nominal Tetap (Rp)</option>
                            </select>
                        </div>

                        <!-- Percentage -->
                        <div id="field-percentage">
                            <label class="block text-sm font-medium mb-2">Persentase (%) *</label>
                            <input type="number" name="percentage" id="discount-percentage" min="0" max="100"
                                step="0.01"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="10" />
                        </div>

                        <!-- Amount -->
                        <div id="field-amount" class="hidden">
                            <label class="block text-sm font-medium mb-2">Nominal (Rp) *</label>
                            <input type="number" name="amount" id="discount-amount" min="0"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="10000" />
                        </div>

                        <!-- Max Discount Amount -->
                        <div id="field-max-discount">
                            <label class="block text-sm font-medium mb-2">Maks Diskon (Rp)</label>
                            <input type="number" name="max_discount_amount" id="discount-max-amount" min="0"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="50000" />
                        </div>

                        <!-- Min Order Amount -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Min Order (Rp)</label>
                            <input type="number" name="min_order_amount" id="discount-min-order" min="0"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="25000" />
                        </div>

                        <!-- Valid From -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Berlaku Dari *</label>
                            <input type="datetime-local" name="valid_from" id="discount-valid-from" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        </div>

                        <!-- Valid Until -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Berlaku Sampai *</label>
                            <input type="datetime-local" name="valid_until" id="discount-valid-until" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        </div>

                        <!-- Usage Limit -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Batas Penggunaan</label>
                            <input type="number" name="usage_limit" id="discount-usage-limit" min="1"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="Kosongkan jika unlimited" />
                        </div>

                        <!-- Usage Per User -->
                        <div>
                            <label class="block text-sm font-medium mb-2">Penggunaan / User</label>
                            <input type="number" name="usage_per_user" id="discount-usage-per-user" min="1"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="1" />
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2">Deskripsi</label>
                            <textarea name="description" id="discount-description" rows="3"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="Deskripsi singkat promo..."></textarea>
                        </div>

                        <!-- Banner Image -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2">Banner Image</label>
                            <input type="file" name="banner_image" id="discount-banner" accept="image/*"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-100 file:text-emerald-700 file:font-medium" />
                        </div>

                        <!-- Toggles -->
                        <div class="flex items-center gap-6 sm:col-span-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" id="discount-active" value="1" checked
                                    class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                                <span class="text-sm font-medium">Aktif</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_banner" id="discount-is-banner" value="1"
                                    class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                                <span class="text-sm font-medium">Tampilkan sebagai Banner</span>
                            </label>
                        </div>
                    </div>

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 p-4 rounded-xl">
                            <ul class="list-disc list-inside text-sm space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="flex-1 bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-xl font-semibold transition">
                            Simpan
                        </button>
                        <button type="button" onclick="closeModal()"
                            class="px-6 py-3 border border-gray-300 dark:border-gray-700 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <div id="delete-modal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8">
                <div class="text-center mb-6">
                    <div
                        class="w-16 h-16 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-2">Hapus Diskon</h3>
                    <p class="text-gray-500">Apakah Anda yakin ingin menghapus diskon ini? Tindakan ini tidak dapat
                        dibatalkan.</p>
                </div>

                <form id="delete-form" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="flex gap-3">
                        <button type="submit"
                            class="flex-1 bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-xl font-semibold transition">
                            Hapus
                        </button>
                        <button type="button" onclick="closeDeleteModal()"
                            class="flex-1 border border-gray-300 dark:border-gray-700 px-6 py-3 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

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
            // Toggle percentage / fixed fields
            // ============================================
            function toggleDiscountFields() {
                const type = document.getElementById('discount-type').value;
                const percentField = document.getElementById('field-percentage');
                const amountField = document.getElementById('field-amount');
                const maxDiscountField = document.getElementById('field-max-discount');

                if (type === 'percentage') {
                    percentField.classList.remove('hidden');
                    amountField.classList.add('hidden');
                    maxDiscountField.classList.remove('hidden');
                } else {
                    percentField.classList.add('hidden');
                    amountField.classList.remove('hidden');
                    maxDiscountField.classList.add('hidden');
                }
            }

            // ============================================
            // Modal Functions
            // ============================================
            function openAddModal() {
                document.getElementById('modal-title').textContent = 'Tambah Diskon Baru';
                document.getElementById('discount-form').reset();
                document.getElementById('discount-form').action = routePrefix + '/discount';
                document.getElementById('form-method').value = 'POST';
                document.getElementById('discount-active').checked = true;
                toggleDiscountFields();
                document.getElementById('discount-modal').classList.remove('hidden');
            }

            function formatDatetimeLocal(dateStr) {
                if (!dateStr) return '';
                const d = new Date(dateStr);
                const pad = n => String(n).padStart(2, '0');
                return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())}T${pad(d.getHours())}:${pad(d.getMinutes())}`;
            }

            function openEditModal(item) {
                document.getElementById('modal-title').textContent = 'Edit Diskon';
                document.getElementById('discount-form').action = routePrefix + '/discount/' + item.id;
                document.getElementById('form-method').value = 'PUT';

                document.getElementById('discount-name').value = item.name || '';
                document.getElementById('discount-code').value = item.code || '';
                document.getElementById('discount-type').value = item.type || 'percentage';
                document.getElementById('discount-percentage').value = item.percentage || '';
                document.getElementById('discount-amount').value = item.amount || '';
                document.getElementById('discount-max-amount').value = item.max_discount_amount || '';
                document.getElementById('discount-min-order').value = item.min_order_amount || '';
                document.getElementById('discount-valid-from').value = formatDatetimeLocal(item.valid_from);
                document.getElementById('discount-valid-until').value = formatDatetimeLocal(item.valid_until);
                document.getElementById('discount-usage-limit').value = item.usage_limit ?? '';
                document.getElementById('discount-usage-per-user').value = item.usage_per_user ?? 1;
                document.getElementById('discount-description').value = item.description || '';
                document.getElementById('discount-active').checked = !!item.is_active;
                document.getElementById('discount-is-banner').checked = !!item.is_banner;

                toggleDiscountFields();
                document.getElementById('discount-modal').classList.remove('hidden');
            }

            function closeModal() {
                document.getElementById('discount-modal').classList.add('hidden');
            }

            function openDeleteModal(id) {
                document.getElementById('delete-form').action = routePrefix + '/discount/' + id;
                document.getElementById('delete-modal').classList.remove('hidden');
            }

            function closeDeleteModal() {
                document.getElementById('delete-modal').classList.add('hidden');
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
