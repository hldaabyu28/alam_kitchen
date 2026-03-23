@extends('layouts.app')

@section('title', 'Manajemen Pajak')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen Pajak</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Kelola pajak yang berlaku untuk transaksi.</p>
    </div>
    
    <button onclick="openModal()" 
        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-medium transition flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah Pajak
    </button>
</div>

<!-- Alerts -->
@if(session('success'))
<div class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    <p>{{ session('success') }}</p>
</div>
@endif

@if($errors->any())
<div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl">
    <div class="flex items-center gap-3 mb-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <p class="font-medium">Terjadi kesalahan:</p>
    </div>
    <ul class="list-disc list-inside text-sm">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<!-- Table -->
<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama Pajak</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Tarif (%)</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($taxes as $tax)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <td class="py-4 px-6">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $tax->name }}</span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        <span class="font-bold text-gray-700 dark:text-gray-300">{{ number_format($tax->rate, 2) }}%</span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        @if($tax->is_active)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600 dark:bg-emerald-400"></span> Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-500 dark:bg-gray-400"></span> Nonaktif
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right">
                        @php
                            $role = auth()->user()->hasRole('super_admin') ? 'super-admin' : 'admin';
                            $updateUrl = url("/{$role}/tax/{$tax->id}");
                            $deleteUrl = url("/{$role}/tax/{$tax->id}");
                        @endphp
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editTax({{ $tax->id }}, '{{ $tax->name }}', {{ $tax->rate }}, {{ $tax->is_active ? 'true' : 'false' }}, '{{ $updateUrl }}')" 
                                class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <form action="{{ $deleteUrl }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pajak ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 rounded-lg transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="py-12 text-center text-gray-500 dark:text-gray-400">
                        Belum ada pajak yang ditambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create/Edit -->
<div id="taxModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-md shadow-xl transform transition-all">
        @php
            $roleBase = auth()->user()->hasRole('super_admin') ? 'super-admin' : 'admin';
        @endphp
        <form id="taxForm" action="{{ url("/{$roleBase}/tax") }}" method="POST">
            @csrf
            <div id="methodContainer"></div>
            
            <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <h3 id="modalTitle" class="text-xl font-bold">Tambah Pajak</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Nama Pajak</label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: PPN 11%"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Tarif (%)</label>
                    <input type="number" step="0.01" name="rate" id="rate" required placeholder="11" min="0" max="100"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <div class="flex items-center gap-2 mt-4">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                        class="w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 rounded focus:ring-emerald-500">
                    <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">Aktifkan Pajak Ini</label>
                </div>
            </div>
            
            <div class="p-6 border-t border-gray-100 dark:border-gray-800 flex gap-3">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition">Batal</button>
                <button type="submit" class="flex-1 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-medium transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const modal = document.getElementById('taxModal');
    const form = document.getElementById('taxForm');
    const methodContainer = document.getElementById('methodContainer');
    const title = document.getElementById('modalTitle');
    
    function openModal() {
        form.reset();
        form.action = '{{ url("/{$roleBase}/tax") }}';
        methodContainer.innerHTML = '';
        title.textContent = 'Tambah Pajak';
        modal.classList.remove('hidden');
    }

    function editTax(id, name, rate, isActive, urlAction) {
        form.action = urlAction;
        methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        document.getElementById('name').value = name;
        document.getElementById('rate').value = rate;
        document.getElementById('is_active').checked = isActive;
        
        title.textContent = 'Edit Pajak';
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
@endsection
