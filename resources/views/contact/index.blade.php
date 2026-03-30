@extends('layouts.app')

@section('title', 'Pesan Masuk')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
            Pesan Masuk
            @if($unreadCount > 0)
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                    {{ $unreadCount }} baru
                </span>
            @endif
        </h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mt-1">Kelola pesan yang dikirim melalui formulir kontak.</p>
    </div>
</div>

{{-- Alerts --}}
@if(session('success'))
<div class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    <p>{{ session('success') }}</p>
</div>
@endif

{{-- Filters --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-4 mb-6">
    <form method="GET" class="flex flex-col sm:flex-row gap-3">
        <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, subjek..."
                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
        </div>
        <select name="status"
            class="px-4 py-2.5 bg-gray-50 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
            <option value="">Semua Status</option>
            <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>Belum Dibaca</option>
            <option value="read"   {{ request('status') === 'read'   ? 'selected' : '' }}>Sudah Dibaca</option>
        </select>
        <button type="submit"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-medium transition text-sm">
            Filter
        </button>
        @if(request()->hasAny(['search','status']))
        <a href="{{ request()->url() }}"
            class="px-5 py-2.5 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition text-sm text-center">
            Reset
        </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Pengirim</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Subjek</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($contacts as $contact)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition {{ !$contact->is_read ? 'bg-emerald-50/40 dark:bg-emerald-900/10' : '' }}">
                    <td class="py-4 px-6">
                        @if(!$contact->is_read)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-red-400"></span> Baru
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                                Dibaca
                            </span>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <div class="font-semibold text-gray-900 dark:text-white text-sm {{ !$contact->is_read ? 'font-bold' : '' }}">
                            {{ $contact->name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $contact->email }}</div>
                        @if($contact->phone)
                            <div class="text-xs text-gray-400 dark:text-gray-500">{{ $contact->phone }}</div>
                        @endif
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-sm text-gray-800 dark:text-gray-200 font-medium">{{ $contact->subject }}</span>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 line-clamp-1">{{ $contact->message }}</p>
                    </td>
                    <td class="py-4 px-6 text-sm text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $contact->created_at->format('d M Y') }}<br>
                        <span class="text-xs">{{ $contact->created_at->format('H:i') }}</span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        @php
                            $roleBase = auth()->user()->hasRole('super_admin') ? 'super-admin' : 'admin';
                        @endphp
                        <div class="flex items-center justify-end gap-2">
                            {{-- View --}}
                            <a href="{{ url("/{$roleBase}/contact/{$contact->id}") }}"
                                class="p-2 text-emerald-600 bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/30 dark:hover:bg-emerald-900/50 rounded-lg transition" title="Lihat Pesan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>

                            {{-- Toggle Read --}}
                            @if(!$contact->is_read)
                            <form action="{{ url("/{$roleBase}/contact/{$contact->id}/read") }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="p-2 text-blue-600 bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/30 dark:hover:bg-blue-900/50 rounded-lg transition" title="Tandai Dibaca">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            </form>
                            @else
                            <form action="{{ url("/{$roleBase}/contact/{$contact->id}/unread") }}" method="POST">
                                @csrf
                                <button type="submit"
                                    class="p-2 text-yellow-600 bg-yellow-50 hover:bg-yellow-100 dark:bg-yellow-900/30 dark:hover:bg-yellow-900/50 rounded-lg transition" title="Tandai Belum Dibaca">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </form>
                            @endif

                            {{-- Delete --}}
                            <form action="{{ url("/{$roleBase}/contact/{$contact->id}") }}" method="POST"
                                onsubmit="return confirm('Hapus pesan ini? Tindakan tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="p-2 text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/30 dark:hover:bg-red-900/50 rounded-lg transition" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-16 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            <p class="font-medium">Belum ada pesan masuk.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($contacts->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-800">
        {{ $contacts->links() }}
    </div>
    @endif
</div>
@endsection
