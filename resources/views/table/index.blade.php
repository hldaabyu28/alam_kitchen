@extends('layouts.app')

@section('title', 'Kelola Meja')
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
            <h2 class="text-3xl sm:text-4xl font-bold mb-2">Kelola Meja</h2>
            <p class="text-gray-500 dark:text-gray-400">Tambah, edit, dan kelola meja restoran</p>
        </div>
        <button onclick="openCreateModal()"
            class="bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-3 rounded-xl font-semibold transition flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Tambah Meja
        </button>
    </div>

    <!-- Stats Cards -->
    @php
        $totalTables = $tables->count();
        $available = $tables->where('status', 'available')->where('is_active', true)->count();
        $occupied = $tables->where('status', 'occupied')->count();
        $reserved = $tables->where('status', 'reserved')->count();
    @endphp
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Total Meja</p>
            <p class="text-3xl font-bold">{{ $totalTables }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Tersedia</p>
            <p class="text-3xl font-bold text-emerald-600">{{ $available }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Terisi</p>
            <p class="text-3xl font-bold text-amber-600">{{ $occupied }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
            <p class="text-xs text-gray-500 uppercase tracking-wider mb-1">Direservasi</p>
            <p class="text-3xl font-bold text-blue-600">{{ $reserved }}</p>
        </div>
    </div>

    <!-- Table Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse ($tables as $table)
            @php
                $statusColors = [
                    'available' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                    'occupied' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300 border-amber-200 dark:border-amber-800',
                    'reserved' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 border-blue-200 dark:border-blue-800',
                ];
                $statusLabels = [
                    'available' => 'Tersedia',
                    'occupied' => 'Terisi',
                    'reserved' => 'Direservasi',
                ];
                $statusIcons = [
                    'available' => '✅',
                    'occupied' => '🍽️',
                    'reserved' => '📅',
                ];
            @endphp
            <div class="bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5 hover:shadow-lg transition {{ !$table->is_active ? 'opacity-50' : '' }}">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold">Meja #{{ $table->table_number }}</h3>
                        @if ($table->location)
                            <p class="text-sm text-gray-500">{{ $table->location }}</p>
                        @endif
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$table->status] ?? '' }}">
                        {{ $statusIcons[$table->status] ?? '' }} {{ $statusLabels[$table->status] ?? $table->status }}
                    </span>
                </div>

                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <span>{{ $table->capacity }} orang</span>
                    </div>
                    @if (!$table->is_active)
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300">Nonaktif</span>
                    @endif
                </div>

                @if ($table->description)
                    <p class="text-sm text-gray-400 mb-4 line-clamp-2">{{ $table->description }}</p>
                @endif

                <div class="flex gap-2">
                    <button onclick='openEditModal(@json($table))'
                        class="flex-1 px-4 py-2 bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300 rounded-xl text-sm font-medium hover:bg-blue-200 dark:hover:bg-blue-800 transition text-center">
                        Edit
                    </button>
                    <form method="POST" action="{{ route($role . '.table.destroy', $table) }}"
                        onsubmit="return confirm('Hapus Meja #{{ $table->table_number }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-4 py-2 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded-xl text-sm font-medium hover:bg-red-200 dark:hover:bg-red-800 transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-12 text-center">
                <div class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">Belum ada meja</h3>
                <p class="text-gray-500">Klik "Tambah Meja" untuk menambahkan meja baru</p>
            </div>
        @endforelse
    </div>

    <!-- ==================== CREATE MODAL ==================== -->
    <div id="create-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8 animate-slideIn">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold">Tambah Meja Baru</h3>
                <button onclick="closeCreateModal()"
                    class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form method="POST" action="{{ route($role . '.table.store') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nomor Meja *</label>
                        <input type="text" name="table_number" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            placeholder="Contoh: 1, A1, VIP-01" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Kapasitas *</label>
                            <input type="number" name="capacity" required min="1" max="50" value="4"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Lokasi</label>
                            <input type="text" name="location"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="Lantai 1, Outdoor" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="description" rows="2"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            placeholder="Deskripsi meja (opsional)"></textarea>
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" checked
                            class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                        <span class="text-sm font-medium">Aktif</span>
                    </label>
                    <button type="submit"
                        class="w-full bg-emerald-700 hover:bg-emerald-800 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Meja
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ==================== EDIT MODAL ==================== -->
    <div id="edit-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8 animate-slideIn">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold">Edit Meja</h3>
                <button onclick="closeEditModal()"
                    class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="edit-form" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nomor Meja *</label>
                        <input type="text" name="table_number" id="edit-table-number" required
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-sm font-medium mb-1">Kapasitas *</label>
                            <input type="number" name="capacity" id="edit-capacity" required min="1" max="50"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Lokasi</label>
                            <input type="text" name="location" id="edit-location"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select name="status" id="edit-status"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="available">✅ Tersedia</option>
                            <option value="occupied">🍽️ Terisi</option>
                            <option value="reserved">📅 Direservasi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea name="description" id="edit-description" rows="2"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                    </div>
                    <label class="flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" id="edit-is-active" value="1"
                            class="w-5 h-5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                        <span class="text-sm font-medium">Aktif</span>
                    </label>
                    <button type="submit"
                        class="w-full bg-emerald-700 hover:bg-emerald-800 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Route prefix
            const currentPath = window.location.pathname;
            let routePrefix = '/admin';
            if (currentPath.startsWith('/super-admin')) routePrefix = '/super-admin';
            else if (currentPath.startsWith('/kasir')) routePrefix = '/kasir';

            // Create Modal
            function openCreateModal() {
                document.getElementById('create-modal').classList.remove('hidden');
            }
            function closeCreateModal() {
                document.getElementById('create-modal').classList.add('hidden');
            }

            // Edit Modal
            function openEditModal(table) {
                document.getElementById('edit-form').action = routePrefix + '/table/' + table.id;
                document.getElementById('edit-table-number').value = table.table_number;
                document.getElementById('edit-capacity').value = table.capacity;
                document.getElementById('edit-location').value = table.location || '';
                document.getElementById('edit-status').value = table.status;
                document.getElementById('edit-description').value = table.description || '';
                document.getElementById('edit-is-active').checked = table.is_active;
                document.getElementById('edit-modal').classList.remove('hidden');
            }
            function closeEditModal() {
                document.getElementById('edit-modal').classList.add('hidden');
            }

            // Auto-dismiss notification
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
