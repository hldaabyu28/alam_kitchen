@extends('layouts.app')

@section('title', 'Manajemen User')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Manajemen User</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Kelola akses dan role pengguna sistem.</p>
    </div>
    
    <button onclick="openModal()" 
        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-medium transition flex items-center justify-center gap-2">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
        </svg>
        Tambah User
    </button>
</div>

<!-- Alerts -->
@if(session('success'))
<div class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
    <p>{{ session('success') }}</p>
</div>
@endif

@if(session('error'))
<div class="mb-6 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 px-4 py-3 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
    <p>{{ session('error') }}</p>
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
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Role</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <td class="py-4 px-6">
                        <span class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</span>
                    </td>
                    <td class="py-4 px-6 text-gray-700 dark:text-gray-300">
                        {{ $user->email }}
                    </td>
                    <td class="py-4 px-6 text-center">
                        @php $roleName = $user->roles->first()?->name; @endphp
                        @if($roleName === 'super_admin')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                Super Admin
                            </span>
                        @elseif($roleName === 'admin')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                Admin
                            </span>
                        @elseif($roleName === 'kasir')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                Kasir
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400">
                                Kosong
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6 text-right">
                        @php
                            $updateUrl = route('super_admin.users.update', $user->id);
                            $deleteUrl = route('super_admin.users.destroy', $user->id);
                        @endphp
                        <div class="flex items-center justify-end gap-2">
                            <button onclick="editUser({{ $user->id }}, '{{ $user->name }}', '{{ $user->email }}', '{{ $roleName }}', '{{ $updateUrl }}')" 
                                class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            </button>
                            <form action="{{ $deleteUrl }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
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
                        Belum ada user yang ditambahkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create/Edit -->
<div id="userModal" class="fixed inset-0 z-50 hidden bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl w-full max-w-md shadow-xl transform transition-all">
        <form id="userForm" action="{{ route('super_admin.users.store') }}" method="POST">
            @csrf
            <div id="methodContainer"></div>
            
            <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center">
                <h3 id="modalTitle" class="text-xl font-bold">Tambah User</h3>
                <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5" for="name">Nama</label>
                    <input type="text" name="name" id="name" required placeholder="Contoh: Budi Santoso"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5" for="email">Email</label>
                    <input type="email" name="email" id="email" required placeholder="budi@example.com"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5" for="role">Role</label>
                    <select name="role" id="role" required class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500 appearance-none">
                        <option value="">-- Pilih Role --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}">{{ Str::title(str_replace('_', ' ', $role->name)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5" for="password">Password <span id="passwordDesc" class="font-normal text-xs text-gray-500 hidden">(Kosongkan jika tidak ingin mengubah)</span></label>
                    <input type="password" name="password" id="password" minlength="8" placeholder="Minimal 8 karakter"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5" for="password_confirmation">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" minlength="8" placeholder="Ketik ulang password"
                        class="w-full px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-500">
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
    const modal = document.getElementById('userModal');
    const form = document.getElementById('userForm');
    const methodContainer = document.getElementById('methodContainer');
    const title = document.getElementById('modalTitle');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const roleInput = document.getElementById('role');
    const passwordInput = document.getElementById('password');
    const passwordDesc = document.getElementById('passwordDesc');
    
    function openModal() {
        form.reset();
        form.action = '{{ route("super_admin.users.store") }}';
        methodContainer.innerHTML = '';
        title.textContent = 'Tambah User';
        
        passwordInput.required = true;
        passwordDesc.classList.add('hidden');
        
        modal.classList.remove('hidden');
    }

    function editUser(id, name, email, role, urlAction) {
        form.reset();
        form.action = urlAction;
        methodContainer.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        
        nameInput.value = name;
        emailInput.value = email;
        roleInput.value = role;
        
        passwordInput.required = false;
        passwordDesc.classList.remove('hidden');
        
        title.textContent = 'Edit User';
        modal.classList.remove('hidden');
    }

    function closeModal() {
        modal.classList.add('hidden');
    }
</script>
@endpush
@endsection
