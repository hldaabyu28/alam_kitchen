@extends('layouts.app')

@section('title', 'Detail Pesan')

@section('content')
@php
    $roleBase = auth()->user()->hasRole('super_admin') ? 'super-admin' : 'admin';
@endphp

{{-- Back link & header --}}
<div class="mb-6 flex items-center gap-4">
    <a href="{{ url("/{$roleBase}/contact") }}"
        class="p-2 text-gray-500 hover:text-gray-900 dark:hover:text-white bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <div>
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pesan</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm">Diterima {{ $contact->created_at->diffForHumans() }}</p>
    </div>
</div>

@if(session('success'))
<div class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3">
    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
    {{ session('success') }}
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Message body --}}
    <div class="lg:col-span-2 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6">
        <div class="flex items-start justify-between gap-4 mb-6">
            <div>
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $contact->subject }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $contact->created_at->format('d F Y, H:i') }} WIB</p>
            </div>
            @if($contact->is_read)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400 whitespace-nowrap">
                    Sudah Dibaca
                </span>
            @else
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 whitespace-nowrap">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-600 dark:bg-red-400"></span> Baru
                </span>
            @endif
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-5 text-gray-700 dark:text-gray-300 leading-relaxed text-sm whitespace-pre-wrap">{{ $contact->message }}</div>

        {{-- Reply via email --}}
        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-800">
            <a href="mailto:{{ $contact->email }}?subject=Re: {{ rawurlencode($contact->subject) }}"
                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl font-medium transition text-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
                Balas via Email
            </a>
        </div>
    </div>

    {{-- Sender info & actions --}}
    <div class="space-y-5">

        {{-- Sender card --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Informasi Pengirim</h4>

            <div class="flex items-center gap-3 mb-5">
                <div class="w-12 h-12 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center text-white font-bold text-lg">
                    {{ strtoupper(substr($contact->name, 0, 1)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $contact->name }}</p>
                    <p class="text-xs text-gray-500">{{ $contact->email }}</p>
                </div>
            </div>

            <div class="space-y-3 text-sm">
                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    <a href="mailto:{{ $contact->email }}" class="hover:text-emerald-600 transition">{{ $contact->email }}</a>
                </div>
                @if($contact->phone)
                <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                    <a href="tel:{{ $contact->phone }}" class="hover:text-emerald-600 transition">{{ $contact->phone }}</a>
                </div>
                @endif
                <div class="flex items-center gap-2 text-gray-500 dark:text-gray-500">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ $contact->created_at->format('d M Y, H:i') }}
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-6 space-y-3">
            <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-4">Tindakan</h4>

            @if(!$contact->is_read)
            <form action="{{ url("/{$roleBase}/contact/{$contact->id}/read") }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-xl font-medium transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    Tandai Sudah Dibaca
                </button>
            </form>
            @else
            <form action="{{ url("/{$roleBase}/contact/{$contact->id}/unread") }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-yellow-50 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-900/50 rounded-xl font-medium transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Tandai Belum Dibaca
                </button>
            </form>
            @endif

            <form action="{{ url("/{$roleBase}/contact/{$contact->id}") }}" method="POST"
                onsubmit="return confirm('Hapus pesan ini secara permanen?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/50 rounded-xl font-medium transition text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Hapus Pesan
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
