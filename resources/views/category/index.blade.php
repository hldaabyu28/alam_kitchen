@extends('layouts.app')

@section('title', 'Kategori Menu')
@section('content')
    <!-- CATEGORY MANAGEMENT CONTENT -->

    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl sm:text-4xl font-bold mb-2">Kategori Menu</h2>
            <p class="text-gray-500 dark:text-gray-400">Kelola kategori menu dengan mudah</p>
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
                    Tambah Kategori
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
                        <th class="px-6 py-4 text-sm font-semibold">Gambar</th>
                        <th class="px-6 py-4 text-sm font-semibold">Nama</th>
                        <th class="px-6 py-4 text-sm font-semibold">Slug</th>
                        <th class="px-6 py-4 text-sm font-semibold">Jumlah Menu</th>
                        <th class="px-6 py-4 text-sm font-semibold">Urutan</th>
                        <th class="px-6 py-4 text-sm font-semibold">Status</th>
                        @if ($canManage)
                            <th class="px-6 py-4 text-sm font-semibold text-right">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                    @forelse ($categories as $category)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <td class="px-6 py-4">
                                @if ($category->image)
                                    <img src="{{ asset('storage/' . $category->image) }}" alt="{{ $category->name }}"
                                        class="w-12 h-12 rounded-xl object-cover">
                                @else
                                    <div
                                        class="w-12 h-12 bg-gray-100 dark:bg-gray-800 rounded-xl flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold">{{ $category->name }}</p>
                                    <p class="text-sm text-gray-500 line-clamp-1">{{ $category->description ?? '' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm text-gray-500 font-mono">{{ $category->slug }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300">
                                    {{ $category->menus_count }} menu
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-gray-600 dark:text-gray-400 font-medium">{{ $category->order }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($category->is_active)
                                    <span
                                        class="px-3 py-1 bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300 rounded-full text-xs font-medium">
                                        Aktif
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded-full text-xs font-medium">
                                        Non-aktif
                                    </span>
                                @endif
                            </td>
                            @if ($canManage)
                                <td class="px-6 py-4 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button onclick='openEditModal(@json($category))'
                                            class="px-4 py-2 bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300 rounded-xl text-sm font-medium hover:bg-emerald-200 dark:hover:bg-emerald-800 transition">
                                            Edit
                                        </button>
                                        <button onclick="openDeleteModal({{ $category->id }})"
                                            class="px-4 py-2 bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300 rounded-xl text-sm font-medium hover:bg-red-200 dark:hover:bg-red-800 transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $canManage ? 7 : 6 }}" class="px-6 py-12 text-center">
                                <div
                                    class="w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a4 4 0 014-4z">
                                        </path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold mb-2">Belum ada kategori</h3>
                                <p class="text-gray-500 mb-4">Mulai tambahkan kategori pertama Anda</p>
                                @if ($canManage)
                                    <button onclick="openAddModal()"
                                        class="bg-emerald-700 hover:bg-emerald-800 text-white px-6 py-2 rounded-xl font-medium transition">
                                        Tambah Kategori
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
        <div id="category-modal"
            class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div
                class="bg-white dark:bg-gray-900 rounded-3xl max-w-2xl w-full p-6 sm:p-8 max-h-[90vh] overflow-y-auto animate-slideIn">
                <div class="flex justify-between items-center mb-6">
                    <h3 id="modal-title" class="text-2xl font-bold">Tambah Kategori Baru</h3>
                    <button onclick="closeModal()"
                        class="w-10 h-10 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg flex items-center justify-center transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>
                </div>

                <form id="category-form" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <input type="hidden" id="form-method" name="_method" value="POST" />

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2">Nama Kategori *</label>
                            <input type="text" name="name" id="category-name" required
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="contoh: Appetizers" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">Urutan</label>
                            <input type="number" name="order" id="category-order" min="0"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="0" />
                        </div>

                        <div>
                            <label class="flex items-center gap-2 cursor-pointer mt-8">
                                <input type="checkbox" name="is_active" id="category-active" value="1" checked
                                    class="w-4 h-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500" />
                                <span class="text-sm font-medium">Aktif</span>
                            </label>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2">Deskripsi</label>
                            <textarea name="description" id="category-description" rows="3"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                placeholder="Deskripsi singkat kategori..."></textarea>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium mb-2">Gambar</label>
                            <input type="file" name="image" id="category-image" accept="image/*"
                                class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-emerald-100 file:text-emerald-700 file:font-medium" />
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
                    <h3 class="text-xl font-bold mb-2">Hapus Kategori</h3>
                    <p class="text-gray-500">Apakah Anda yakin ingin menghapus kategori ini? Tindakan ini tidak dapat
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
            let routePrefix = '/admin'; // default
            if (currentPath.startsWith('/super-admin')) {
                routePrefix = '/super-admin';
            } else if (currentPath.startsWith('/kasir')) {
                routePrefix = '/kasir';
            }

            // ============================================
            // Modal Functions
            // ============================================
            function openAddModal() {
                document.getElementById('modal-title').textContent = 'Tambah Kategori Baru';
                document.getElementById('category-form').reset();
                document.getElementById('category-form').action = routePrefix + '/category';
                document.getElementById('form-method').value = 'POST';
                document.getElementById('category-active').checked = true;
                document.getElementById('category-modal').classList.remove('hidden');
            }

            function openEditModal(item) {
                document.getElementById('modal-title').textContent = 'Edit Kategori';
                document.getElementById('category-form').action = routePrefix + '/category/' + item.id;
                document.getElementById('form-method').value = 'PUT';

                document.getElementById('category-name').value = item.name || '';
                document.getElementById('category-description').value = item.description || '';
                document.getElementById('category-order').value = item.order ?? 0;
                document.getElementById('category-active').checked = !!item.is_active;

                document.getElementById('category-modal').classList.remove('hidden');
            }

            function closeModal() {
                document.getElementById('category-modal').classList.add('hidden');
            }

            function openDeleteModal(id) {
                document.getElementById('delete-form').action = routePrefix + '/category/' + id;
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
