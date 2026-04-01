@extends('layouts.app')

@section('title', 'Pengaturan Toko')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Page Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-800 dark:text-white">⚙️ Pengaturan Toko</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola informasi toko, jam operasional, media sosial, FAQ, dan lainnya.</p>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
    <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-5 py-4 rounded-2xl flex items-center gap-3">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 px-5 py-4 rounded-2xl">
        <ul class="list-disc list-inside space-y-1 text-sm">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="flex gap-1 mb-6 bg-gray-100 dark:bg-gray-800 rounded-2xl p-1.5 overflow-x-auto" id="settings-tabs">
        <button type="button" onclick="switchTab('info')"
            class="settings-tab active-tab px-4 py-2.5 rounded-xl text-sm font-medium transition whitespace-nowrap"
            data-tab="info">
            🏪 Informasi Toko
        </button>
        <button type="button" onclick="switchTab('hours')"
            class="settings-tab px-4 py-2.5 rounded-xl text-sm font-medium transition whitespace-nowrap"
            data-tab="hours">
            🕐 Jam Operasional
        </button>
        <button type="button" onclick="switchTab('maps')"
            class="settings-tab px-4 py-2.5 rounded-xl text-sm font-medium transition whitespace-nowrap"
            data-tab="maps">
            📍 Google Maps
        </button>
        <button type="button" onclick="switchTab('social')"
            class="settings-tab px-4 py-2.5 rounded-xl text-sm font-medium transition whitespace-nowrap"
            data-tab="social">
            📱 Media Sosial & WhatsApp
        </button>
        <button type="button" onclick="switchTab('about')"
            class="settings-tab px-4 py-2.5 rounded-xl text-sm font-medium transition whitespace-nowrap"
            data-tab="about">
            📝 About Us
        </button>
        <button type="button" onclick="switchTab('faq')"
            class="settings-tab px-4 py-2.5 rounded-xl text-sm font-medium transition whitespace-nowrap"
            data-tab="faq">
            ❓ FAQ
        </button>
    </div>

    {{-- Store Settings Form --}}
    @php
        $user = Auth::user();
        $role = 'admin';
        if ($user && $user->hasRole('super_admin')) {
            $role = 'super_admin';
        }
        $routePrefix = $role === 'super_admin' ? 'super_admin' : 'admin';
    @endphp

    <form method="POST" action="{{ route($routePrefix . '.settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- ===== TAB: Informasi Toko ===== --}}
        <div id="tab-info" class="tab-content">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center text-sm">🏪</span>
                    Informasi Toko
                </h2>

                <div class="space-y-5">
                    {{-- Logo --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Logo Toko</label>
                        <div class="flex items-center gap-4">
                            @if($store && $store->logo)
                                <img src="{{ asset('storage/' . $store->logo) }}" alt="Logo" class="w-20 h-20 rounded-2xl object-cover border border-gray-200 dark:border-gray-700">
                            @else
                                <div class="w-20 h-20 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center border border-gray-200 dark:border-gray-700">
                                    <span class="text-2xl">🏪</span>
                                </div>
                            @endif
                            <div>
                                <input type="file" name="logo" accept="image/*" class="text-sm file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-900/20 dark:file:text-emerald-400">
                                <p class="text-xs text-gray-500 mt-1">Max 2MB. Format: JPG, PNG, SVG, WebP</p>
                            </div>
                        </div>
                    </div>

                    {{-- Name --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Nama Toko <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $store->name ?? '') }}" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                            placeholder="Nama toko Anda">
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Deskripsi Singkat</label>
                        <textarea name="description" rows="3"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none"
                            placeholder="Deskripsi singkat tentang toko Anda">{{ old('description', $store->description ?? '') }}</textarea>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Alamat <span class="text-red-500">*</span></label>
                        <textarea name="address" rows="2" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none"
                            placeholder="Alamat lengkap toko">{{ old('address', $store->address ?? '') }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Phone --}}
                        <div>
                            <label class="block text-sm font-medium mb-2">Telepon</label>
                            <input type="text" name="phone" value="{{ old('phone', $store->phone ?? '') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="+62 812-3456-7890">
                        </div>

                        {{-- Email --}}
                        <div>
                            <label class="block text-sm font-medium mb-2">Email</label>
                            <input type="email" name="email" value="{{ old('email', $store->email ?? '') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="hello@alamkitchen.com">
                        </div>
                    </div>

                    {{-- Active Status --}}
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="is_active" id="is_active" value="1"
                            {{ old('is_active', $store->is_active ?? true) ? 'checked' : '' }}
                            class="w-4 h-4 text-emerald-600 rounded focus:ring-emerald-500">
                        <label for="is_active" class="text-sm font-medium">Toko Aktif</label>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TAB: Jam Operasional ===== --}}
        <div id="tab-hours" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center text-sm">🕐</span>
                    Jam Operasional
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-2">Jam Buka</label>
                        <input type="time" name="opening_time"
                            value="{{ old('opening_time', $store && $store->opening_time ? $store->opening_time->format('H:i') : '') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Jam Tutup</label>
                        <input type="time" name="closing_time"
                            value="{{ old('closing_time', $store && $store->closing_time ? $store->closing_time->format('H:i') : '') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                    </div>
                </div>

                <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 rounded-2xl p-4 text-sm text-blue-700 dark:text-blue-300">
                    <p>💡 Jam operasional akan ditampilkan di landing page pada bagian Contact Info dan Footer.</p>
                </div>
            </div>
        </div>

        {{-- ===== TAB: Google Maps ===== --}}
        <div id="tab-maps" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center text-sm">📍</span>
                    Google Maps
                </h2>

                <div class="space-y-5">
                    <div>
                        <label class="block text-sm font-medium mb-2">Google Maps URL</label>
                        <input type="url" name="google_maps_url" value="{{ old('google_maps_url', $store->google_maps_url ?? '') }}"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                            placeholder="https://maps.google.com/...">
                        <p class="text-xs text-gray-500 mt-1">Link Google Maps yang bisa diklik oleh pengunjung.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Google Maps Embed Code</label>
                        <textarea name="google_maps_embed" rows="4"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm font-mono resize-none"
                            placeholder='<iframe src="https://www.google.com/maps/embed?..." ...></iframe>'>{{ old('google_maps_embed', $store->google_maps_embed ?? '') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Kode embed &lt;iframe&gt; dari Google Maps. Embed ini akan tampil di landing page.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-2">Latitude</label>
                            <input type="number" step="any" name="latitude" value="{{ old('latitude', $store->latitude ?? '') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="-6.2088">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-2">Longitude</label>
                            <input type="number" step="any" name="longitude" value="{{ old('longitude', $store->longitude ?? '') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="106.8456">
                        </div>
                    </div>

                    @if($store && $store->google_maps_embed)
                    <div>
                        <label class="block text-sm font-medium mb-2">Preview Maps</label>
                        <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700">
                            {!! $store->google_maps_embed !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== TAB: Media Sosial & WhatsApp ===== --}}
        <div id="tab-social" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center text-sm">📱</span>
                    Media Sosial & WhatsApp
                </h2>

                <div class="space-y-5">
                    {{-- WhatsApp --}}
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-2xl p-5 border border-green-200 dark:border-green-800">
                        <label class="block text-sm font-semibold mb-2 text-green-700 dark:text-green-400">💬 Nomor WhatsApp (Floating Button)</label>
                        <input type="text" name="whatsapp_number" value="{{ old('whatsapp_number', $store->whatsapp_number ?? '') }}"
                            class="w-full px-4 py-3 rounded-xl border border-green-300 dark:border-green-700 bg-white dark:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm"
                            placeholder="6281234567890">
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">Format: 6281234567890 (tanpa + atau spasi). Tombol WhatsApp akan muncul di landing page.</p>
                    </div>

                    {{-- Social Media --}}
                    <div>
                        <label class="block text-sm font-medium mb-2">Instagram</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">instagram.com/</span>
                            <input type="text" name="instagram" value="{{ old('instagram', $store->instagram ?? '') }}"
                                class="flex-1 px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="alamkitchen">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Facebook</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">facebook.com/</span>
                            <input type="text" name="facebook" value="{{ old('facebook', $store->facebook ?? '') }}"
                                class="flex-1 px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="alamkitchen">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">TikTok</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">tiktok.com/@</span>
                            <input type="text" name="tiktok" value="{{ old('tiktok', $store->tiktok ?? '') }}"
                                class="flex-1 px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="alamkitchen">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-2">Twitter / X</label>
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500">x.com/</span>
                            <input type="text" name="twitter" value="{{ old('twitter', $store->twitter ?? '') }}"
                                class="flex-1 px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                                placeholder="alamkitchen">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== TAB: About Us ===== --}}
        <div id="tab-about" class="tab-content hidden">
            <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-800">
                <h2 class="text-lg font-semibold mb-6 flex items-center gap-2">
                    <span class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center text-sm">📝</span>
                    About Us
                </h2>

                <div>
                    <label class="block text-sm font-medium mb-2">Konten About Us</label>
                    <textarea name="about_us" rows="10"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none"
                        placeholder="Ceritakan tentang toko Anda, visi & misi, sejarah, dll...">{{ old('about_us', $store->about_us ?? '') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Teks ini akan ditampilkan di bagian About pada landing page.</p>
                </div>
            </div>
        </div>

        {{-- Save Button (shared across all tabs except FAQ) --}}
        <div class="mt-6" id="save-btn-container">
            <button type="submit"
                class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3 rounded-xl font-semibold transition shadow-lg shadow-emerald-600/20 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                Simpan Pengaturan
            </button>
        </div>
    </form>

    {{-- ===== TAB: FAQ ===== --}}
    <div id="tab-faq" class="tab-content hidden">
        <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-8 shadow-sm border border-gray-100 dark:border-gray-800">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold flex items-center gap-2">
                    <span class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center text-sm">❓</span>
                    FAQ (Frequently Asked Questions)
                </h2>
                <button type="button" onclick="openFaqModal()"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-semibold transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah FAQ
                </button>
            </div>

            @if($faqs->isEmpty())
                <div class="text-center py-12 text-gray-500">
                    <p class="text-4xl mb-3">❓</p>
                    <p class="font-medium">Belum ada FAQ</p>
                    <p class="text-sm mt-1">Tambahkan FAQ untuk ditampilkan di landing page.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($faqs as $faq)
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-5 border border-gray-100 dark:border-gray-700 hover:shadow-md transition">
                            <div class="flex justify-between items-start gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-sm">{{ $faq->question }}</span>
                                        @if($faq->is_active)
                                            <span class="text-xs bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 px-2 py-0.5 rounded-full">Aktif</span>
                                        @else
                                            <span class="text-xs bg-gray-200 text-gray-500 dark:bg-gray-700 dark:text-gray-400 px-2 py-0.5 rounded-full">Nonaktif</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">{{ $faq->answer }}</p>
                                    @if($faq->category)
                                        <span class="text-xs text-gray-500 mt-1 inline-block">📁 {{ $faq->category }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <button type="button" onclick="openEditFaqModal({{ $faq->id }}, '{{ addslashes($faq->question) }}', `{{ addslashes($faq->answer) }}`, '{{ addslashes($faq->category ?? '') }}', {{ $faq->order }}, {{ $faq->is_active ? 'true' : 'false' }})"
                                        class="text-gray-500 hover:text-emerald-600 transition p-1.5 rounded-lg hover:bg-emerald-50 dark:hover:bg-emerald-900/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <form method="POST" action="{{ route($routePrefix . '.settings.faq.destroy', $faq) }}" onsubmit="return confirm('Hapus FAQ ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-500 hover:text-red-600 transition p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

{{-- FAQ Add Modal --}}
<div id="faq-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl transform scale-95 transition-transform duration-300" id="faq-modal-content">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold">Tambah FAQ</h3>
            <button onclick="closeFaqModal()" class="text-2xl hover:text-red-500 transition">&times;</button>
        </div>

        <form id="faq-add-form" method="POST" action="{{ route($routePrefix . '.settings.faq.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium mb-1.5">Pertanyaan <span class="text-red-500">*</span></label>
                <input type="text" name="question" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                    placeholder="Contoh: Apakah bisa reservasi online?">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Jawaban <span class="text-red-500">*</span></label>
                <textarea name="answer" rows="4" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none"
                    placeholder="Jawaban lengkap untuk pertanyaan di atas..."></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Kategori</label>
                    <input type="text" name="category"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm"
                        placeholder="Umum / Reservasi / dll">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Urutan</label>
                    <input type="number" name="order" value="0" min="0"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="faq-active" value="1" checked class="w-4 h-4 text-emerald-600 rounded">
                <label for="faq-active" class="text-sm">Aktif</label>
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold transition">
                Simpan FAQ
            </button>
        </form>
    </div>
</div>

{{-- FAQ Edit Modal --}}
<div id="faq-edit-modal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl transform scale-95 transition-transform duration-300" id="faq-edit-modal-content">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold">Edit FAQ</h3>
            <button onclick="closeEditFaqModal()" class="text-2xl hover:text-red-500 transition">&times;</button>
        </div>

        <form id="faq-edit-form" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-medium mb-1.5">Pertanyaan <span class="text-red-500">*</span></label>
                <input type="text" name="question" id="edit-faq-question" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1.5">Jawaban <span class="text-red-500">*</span></label>
                <textarea name="answer" id="edit-faq-answer" rows="4" required
                    class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm resize-none"></textarea>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1.5">Kategori</label>
                    <input type="text" name="category" id="edit-faq-category"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1.5">Urutan</label>
                    <input type="number" name="order" id="edit-faq-order" min="0"
                        class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                </div>
            </div>
            <div class="flex items-center gap-2">
                <input type="checkbox" name="is_active" id="edit-faq-active" value="1" class="w-4 h-4 text-emerald-600 rounded">
                <label for="edit-faq-active" class="text-sm">Aktif</label>
            </div>
            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-3 rounded-xl font-semibold transition">
                Simpan Perubahan
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Tab switching
    function switchTab(tab) {
        // Hide all tabs
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        // Show selected tab
        document.getElementById('tab-' + tab).classList.remove('hidden');

        // Update tab buttons
        document.querySelectorAll('.settings-tab').forEach(btn => {
            btn.classList.remove('active-tab', 'bg-white', 'dark:bg-gray-900', 'shadow-sm', 'text-gray-900', 'dark:text-white');
            btn.classList.add('text-gray-500', 'hover:text-gray-700');
        });
        const activeBtn = document.querySelector(`.settings-tab[data-tab="${tab}"]`);
        activeBtn.classList.add('active-tab', 'bg-white', 'dark:bg-gray-900', 'shadow-sm', 'text-gray-900', 'dark:text-white');
        activeBtn.classList.remove('text-gray-500', 'hover:text-gray-700');

        // Show/hide save button (not needed for FAQ tab)
        const saveBtn = document.getElementById('save-btn-container');
        if (tab === 'faq') {
            saveBtn.classList.add('hidden');
        } else {
            saveBtn.classList.remove('hidden');
        }
    }

    // Initialize first tab as active
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('info');
    });

    // FAQ Modal
    function openFaqModal() {
        const modal = document.getElementById('faq-modal');
        const content = document.getElementById('faq-modal-content');
        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeFaqModal() {
        const modal = document.getElementById('faq-modal');
        const content = document.getElementById('faq-modal-content');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }

    function openEditFaqModal(id, question, answer, category, order, isActive) {
        const modal = document.getElementById('faq-edit-modal');
        const content = document.getElementById('faq-edit-modal-content');
        const form = document.getElementById('faq-edit-form');

        // Build the URL using the route prefix
        const baseUrl = "{{ url(request()->segment(1)) }}";
        form.action = baseUrl + '/settings/faq/' + id;

        document.getElementById('edit-faq-question').value = question;
        document.getElementById('edit-faq-answer').value = answer;
        document.getElementById('edit-faq-category').value = category;
        document.getElementById('edit-faq-order').value = order;
        document.getElementById('edit-faq-active').checked = isActive;

        modal.classList.remove('hidden');
        setTimeout(() => {
            content.classList.remove('scale-95');
            content.classList.add('scale-100');
        }, 10);
    }

    function closeEditFaqModal() {
        const modal = document.getElementById('faq-edit-modal');
        const content = document.getElementById('faq-edit-modal-content');
        content.classList.remove('scale-100');
        content.classList.add('scale-95');
        setTimeout(() => modal.classList.add('hidden'), 200);
    }
</script>
@endpush
