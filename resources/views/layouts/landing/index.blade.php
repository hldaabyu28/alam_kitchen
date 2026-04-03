<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Alam Kitchen | @yield('title')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap"
        rel="stylesheet">
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        h1,
        h2,
        h3 {
            font-family: 'Playfair Display', serif;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .cart-badge {
            animation: pulse 0.3s ease-in-out;
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.2);
            }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-track {
            background: #1f2937;
        }

        .dark ::-webkit-scrollbar-thumb {
            background: #4b5563;
        }
    </style>
</head>

<body class="bg-[#f7f6f4] dark:bg-gray-950 text-gray-800 dark:text-gray-200 transition duration-300">

    <!-- NAVBAR -->
    <nav
        class="fixed w-full z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-xl border-b border-gray-200/30 dark:border-gray-700/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-4 flex justify-between items-center">

            <!-- Mobile Menu Button -->
            <button onclick="toggleMobileMenu()" class="lg:hidden border rounded-full p-2 hover:scale-110 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16">
                    </path>
                </svg>
            </button>

            <!-- Logo -->
            <a href="#home" class="flex items-center gap-2.5 group">
                @if($store && $store->logo)
                <img src="{{ asset('storage/' . $store->logo) }}"
                    alt="{{ $store->name ?? 'Alam Kitchen' }}"
                    class="h-9 w-auto object-contain transition group-hover:scale-105">
                @else
                <span class="w-9 h-9 rounded-full bg-orange-500 flex items-center justify-center text-white font-bold text-base flex-shrink-0 transition group-hover:scale-105">
                    {{ strtoupper(substr($store->name ?? 'A', 0, 1)) }}
                </span>
                @endif
                <h1 class="tracking-[0.3em] font-semibold text-xs sm:text-sm md:text-base">
                    {{ $store->name ?? 'Alam Kitchen' }}
                </h1>
            </a>
            <!-- Desktop Menu -->
            <div class="hidden lg:flex items-center gap-6">
                <a href="#home" class="hover:text-orange-500 transition text-sm">Home</a>
                <a href="#menu" class="hover:text-orange-500 transition text-sm">Menu</a>
                <a href="#about" class="hover:text-orange-500 transition text-sm">About</a>
                <a href="#contact" class="hover:text-orange-500 transition text-sm">Contact</a>
            </div>

            <!-- Right Side Buttons -->
            <div class="flex items-center gap-2 sm:gap-3">
                <!-- Cart Button -->
                <button onclick="toggleCart()" class="relative border p-2 rounded-full hover:scale-110 transition">
                    🛒
                    <span id="cart-count"
                        class="absolute -top-1 -right-1 bg-orange-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center hidden">0</span>
                </button>

                <!-- Book Table Button -->
                <button onclick="openBookingModal()"
                    class="hidden sm:block border px-4 md:px-5 py-2 rounded-full text-xs md:text-sm hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black transition">
                    Book Table
                </button>

                <!-- Dark Mode Toggle -->
                <button onclick="toggleDarkMode()" class="border p-2 rounded-full hover:scale-110 transition">
                    <span id="dark-mode-icon">🌙</span>
                </button>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden lg:hidden border-t border-gray-200 dark:border-gray-700">
            <div class="px-4 py-4 space-y-3">
                <a href="#home" class="block hover:text-orange-500 transition">Home</a>
                <a href="#menu" class="block hover:text-orange-500 transition">Menu</a>
                <a href="#about" class="block hover:text-orange-500 transition">About</a>
                <a href="#contact" class="block hover:text-orange-500 transition">Contact</a>
                <button onclick="openBookingModal()"
                    class="w-full border px-5 py-2 rounded-full text-sm hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black transition">
                    Book Table
                </button>
            </div>
        </div>
    </nav>

    <!-- Cart Sidebar -->
    <div id="cart-sidebar"
        class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white dark:bg-gray-900 shadow-2xl z-50 transform translate-x-full transition-transform duration-300">
        <div class="flex flex-col h-full">
            <!-- Cart Header -->
            <div class="flex justify-between items-center p-6 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-xl font-semibold">Your Cart</h3>
                <button onclick="toggleCart()" class="text-2xl hover:text-orange-500 transition">&times;</button>
            </div>

            <!-- Cart Items -->
            <div id="cart-items" class="flex-1 overflow-y-auto p-6">
                <p class="text-center text-gray-500 py-8">Your cart is empty</p>
            </div>

            <!-- Cart Footer -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-6">
                <div class="flex justify-between mb-4">
                    <span class="font-semibold">Total:</span>
                    <span id="cart-total" class="font-bold text-xl">$0.00</span>
                </div>
                <button onclick="checkout()"
                    class="w-full bg-orange-500 text-white py-3 rounded-full hover:bg-orange-600 transition font-semibold">
                    Checkout
                </button>
            </div>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div id="checkout-modal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8 transform scale-95 transition-transform duration-300 max-h-[90vh] overflow-y-auto"
            id="checkout-modal-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold">Checkout</h3>
                <button onclick="closeCheckoutModal()" class="text-2xl hover:text-orange-500 transition">&times;</button>
            </div>

            <form id="checkout-form" method="POST" action="{{ route('order.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" id="checkout-items-json" name="items_json" />

                <div>
                    <label class="block text-sm mb-2">Nama Lengkap *</label>
                    <input type="text" name="customer_name" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Nama Anda">
                </div>

                <div>
                    <label class="block text-sm mb-2">Email *</label>
                    <input type="email" name="customer_email" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="email@contoh.com">
                </div>

                <div>
                    <label class="block text-sm mb-2">No. Telepon *</label>
                    <input type="tel" name="customer_phone" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="08xxxxxxxxxx">
                </div>

                <div>
                    <label class="block text-sm mb-2">Waktu Pengambilan *</label>
                    <input type="datetime-local" name="pickup_time" required
                        class="w-full px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm mb-2">Catatan (Opsional)</label>
                    <textarea name="notes" rows="2"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Catatan tambahan..."></textarea>
                </div>

                <div id="checkout-summary" class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-4 space-y-2">
                    <!-- Will be populated by JS -->
                </div>

                <div>
                    <label class="block text-sm mb-2">Kode Promo</label>
                    <div class="flex gap-2">
                        <input type="text" id="checkout-promo-input" autocomplete="off"
                            class="w-full px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500 uppercase text-sm"
                            placeholder="KODE PROMO">
                        <button type="button" onclick="applyCheckoutPromo()"
                            class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-5 rounded-2xl text-sm font-semibold hover:bg-black transition whitespace-nowrap">
                            Gunakan
                        </button>
                    </div>
                    <p id="checkout-promo-msg" class="text-xs mt-2 hidden"></p>
                    <input type="hidden" name="discount_code" id="checkout-discount-code">
                </div>

                <button type="submit"
                    class="w-full bg-orange-500 text-white py-3 rounded-full hover:bg-orange-600 transition font-semibold">
                    Pesan Sekarang
                </button>
            </form>
        </div>
    </div>
    <!-- Booking + Pre-Order Modal -->
    <div id="booking-modal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-3xl w-full p-6 sm:p-8 transform scale-95 transition-transform duration-300 max-h-[92vh] overflow-y-auto"
            id="booking-modal-content">

            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-semibold" id="booking-title">Book a Table</h3>
                    <p class="text-sm text-gray-500 mt-1" id="booking-subtitle">Step 1 of 2 — Reservation Details</p>
                </div>
                <button onclick="closeBookingModal()" class="text-2xl hover:text-orange-500 transition">&times;</button>
            </div>

            <!-- Step indicator -->
            <div class="flex gap-2 mb-6">
                <div id="step-bar-1" class="flex-1 h-1.5 rounded-full bg-orange-500 transition-all"></div>
                <div id="step-bar-2" class="flex-1 h-1.5 rounded-full bg-gray-200 dark:bg-gray-700 transition-all"></div>
            </div>

            @if (session('booking_error'))
            <div class="bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 px-4 py-3 rounded-2xl mb-4 text-sm">
                {{ session('booking_error') }}
            </div>
            @endif

            <form id="booking-form" method="POST" action="{{ route('reservation.guest.store') }}">
                @csrf

                <!-- ========== STEP 1: Reservation Details ========== -->
                <div id="booking-step-1" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Nama Lengkap *</label>
                            <input type="text" name="customer_name" id="rsv-name" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="Nama Anda" value="{{ old('customer_name') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">No. Telepon *</label>
                            <input type="tel" name="customer_phone" id="rsv-phone" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                                placeholder="08xxxxxxxxxx" value="{{ old('customer_phone') }}">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Email</label>
                        <input type="email" name="customer_email"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="email@example.com" value="{{ old('customer_email') }}">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Tanggal *</label>
                            <input type="date" name="reservation_date" id="rsv-date" required min="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                                value="{{ old('reservation_date') }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Waktu *</label>
                            <input type="time" name="reservation_time_slot" id="rsv-time" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                                value="{{ old('reservation_time_slot') }}">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium mb-1">Jumlah Tamu *</label>
                            <input type="number" name="guest_count" id="rsv-guests" required min="1" value="{{ old('guest_count', 2) }}"
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Meja *</label>
                            <select name="table_id" id="rsv-table" required
                                class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                                <option value="">Pilih meja</option>
                                @foreach ($tables as $table)
                                <option value="{{ $table->id }}" {{ old('table_id') == $table->id ? 'selected' : '' }}>
                                    #{{ $table->table_number }} ({{ $table->capacity }} pax)
                                    {{ $table->location ? '- ' . $table->location : '' }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">Permintaan Khusus</label>
                        <textarea name="special_requests" rows="2"
                            class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"
                            placeholder="Alergi, preferensi tempat duduk...">{{ old('special_requests') }}</textarea>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="button" onclick="goToStep2()"
                            class="flex-1 bg-orange-500 text-white py-3 rounded-full hover:bg-orange-600 transition font-semibold">
                            🍽️ Lanjut — Pre-Order Menu
                        </button>
                        <button type="submit"
                            class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition font-semibold text-sm">
                            Hanya Reservasi
                        </button>
                    </div>
                </div>

                <!-- ========== STEP 2: Pre-Order Menu ========== -->
                <div id="booking-step-2" class="hidden">
                    <!-- Category Filter -->
                    <div class="flex gap-2 overflow-x-auto pb-3 mb-4 scrollbar-hide">
                        <button type="button" onclick="filterBookingMenu('all')"
                            class="booking-cat-btn active-cat px-4 py-1.5 rounded-full text-sm font-medium border border-gray-300 dark:border-gray-600 whitespace-nowrap transition"
                            data-cat="all">Semua</button>
                        @foreach ($categories as $cat)
                        <button type="button" onclick="filterBookingMenu('{{ $cat->name }}')"
                            class="booking-cat-btn px-4 py-1.5 rounded-full text-sm font-medium border border-gray-300 dark:border-gray-600 whitespace-nowrap transition"
                            data-cat="{{ $cat->name }}">{{ $cat->name }}</button>
                        @endforeach
                    </div>

                    <!-- Menu Grid -->
                    <div id="booking-menu-grid" class="grid grid-cols-2 sm:grid-cols-3 gap-3 max-h-[40vh] overflow-y-auto pr-1 mb-4">
                        @foreach ($menus as $menu)
                        <div class="booking-menu-item bg-gray-50 dark:bg-gray-800 rounded-2xl p-3 transition hover:shadow-md cursor-pointer"
                            data-category="{{ $menu->category ? $menu->category->name : 'Uncategorized' }}"
                            data-id="{{ $menu->id }}"
                            data-name="{{ $menu->name }}"
                            data-price="{{ $menu->price }}"
                            onclick="addBookingItem({{ $menu->id }}, '{{ addslashes($menu->name) }}', {{ $menu->price }})">
                            <img src="{{ $menu->image ? asset('storage/' . $menu->image) : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c' }}"
                                class="w-full h-20 object-cover rounded-xl mb-2" alt="{{ $menu->name }}">
                            <p class="text-xs font-semibold leading-tight mb-1 line-clamp-2">{{ $menu->name }}</p>
                            <p class="text-xs text-orange-500 font-bold">Rp {{ number_format($menu->price, 0, ',', '.') }}</p>
                        </div>
                        @endforeach
                    </div>

                    <!-- Pre-Order Cart -->
                    <div id="booking-order-summary" class="bg-gray-50 dark:bg-gray-800 rounded-2xl p-4 mb-4">
                        <h4 class="font-semibold text-sm mb-2">📋 Pre-Order Anda</h4>
                        <div id="booking-order-items" class="space-y-2 text-sm">
                            <p class="text-gray-400 text-center py-2" id="booking-empty-msg">Belum ada item — klik menu di atas untuk menambahkan</p>
                        </div>

                        <div class="mt-3 pt-3 border-t border-gray-200 dark:border-gray-700 hidden" id="booking-promo-section">
                            <div class="flex gap-2">
                                <input type="text" id="booking-promo-input" autocomplete="off"
                                    class="w-full px-3 py-2 rounded-xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm uppercase"
                                    placeholder="Kode Promo">
                                <button type="button" onclick="applyBookingPromo()"
                                    class="bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-900 px-4 rounded-xl text-sm font-semibold hover:bg-black transition whitespace-nowrap">
                                    Gunakan
                                </button>
                            </div>
                            <p id="booking-promo-msg" class="text-xs mt-1 hidden"></p>
                            <input type="hidden" name="discount_code" id="booking-discount-code">
                        </div>

                        <div id="booking-order-discount-row" class="hidden border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 flex justify-between text-red-500 font-medium text-sm">
                            <span>Diskon</span>
                            <span id="booking-discount-value">- Rp 0</span>
                        </div>

                        <div id="booking-order-total" class="hidden border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 flex justify-between font-bold">
                            <span>Total</span>
                            <span id="booking-total-value">Rp 0</span>
                        </div>
                    </div>

                    <!-- Hidden inputs for items -->
                    <div id="booking-items-inputs"></div>

                    <div class="flex gap-3">
                        <button type="button" onclick="goToStep1()"
                            class="px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition font-semibold text-sm">
                            ← Kembali
                        </button>
                        <button type="submit"
                            class="flex-1 bg-orange-500 text-white py-3 rounded-full hover:bg-orange-600 transition font-semibold">
                            ✅ Konfirmasi Reservasi & Pesanan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Success Notification -->
    <div id="notification"
        class="hidden fixed top-24 right-4 bg-green-500 text-white px-6 py-4 rounded-2xl shadow-lg z-50 animate-slide-in">
        <p id="notification-message">Success!</p>
    </div>

    <!-- HERO -->
    <section id="home" class="pt-28 sm:pt-32 pb-12 sm:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">

            <div data-aos="fade-up">
                <p class="tracking-widest text-xs sm:text-sm mb-4 text-orange-500">HARMONY HOUSE KITCHEN</p>

                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-semibold leading-tight mb-6">
                    Taste Flavors From Around the World
                </h1>

                <p class="opacity-70 mb-8 text-sm sm:text-base max-w-xl leading-relaxed">
                    Where culinary excellence meets a symphony of flavors creating unforgettable dining experience with
                    every bite.
                </p>

                <div
                    class="flex items-center bg-white dark:bg-gray-800 rounded-full shadow-lg px-4 sm:px-5 py-3 w-full max-w-md hover:shadow-xl transition">
                    <input id="search-input" class="bg-transparent flex-1 outline-none text-sm"
                        placeholder="What you want to grab today?" />
                    <button onclick="searchDish()"
                        class="bg-orange-400 text-white px-4 sm:px-5 py-2 rounded-full hover:scale-105 transition ml-2">
                        🔍
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:gap-6" data-aos="zoom-in">
                <img class="rounded-2xl sm:rounded-3xl object-cover h-36 sm:h-40 md:h-48 lg:h-52 w-full hover:scale-105 transition duration-300"
                    src="https://images.unsplash.com/photo-1565299624946-b28f40a0ae38" alt="Food 1" />
                <img class="rounded-2xl sm:rounded-3xl object-cover h-36 sm:h-40 md:h-48 lg:h-52 w-full hover:scale-105 transition duration-300"
                    src="https://images.unsplash.com/photo-1600891964599-f61ba0e24092" alt="Food 2" />
                <img class="rounded-2xl sm:rounded-3xl object-cover h-44 sm:h-52 md:h-60 lg:h-64 w-full col-span-2 hover:scale-105 transition duration-300"
                    src="https://images.unsplash.com/photo-1555396273-367ea4eb4db5" alt="Food 3" />
            </div>
        </div>
    </section>

    <!-- POPULAR DISHES -->
    <section id="menu" class="pb-16 sm:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">

            <div class="flex justify-between items-center mb-8 sm:mb-12">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-semibold">Popular Dishes</h2>
                <div class="flex gap-2">
                    <button onclick="scrollDishes('left')"
                        class="border rounded-full p-2 hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black transition">
                        ←
                    </button>
                    <button onclick="scrollDishes('right')"
                        class="border rounded-full p-2 hover:bg-black hover:text-white dark:hover:bg-white dark:hover:text-black transition">
                        →
                    </button>
                </div>
            </div>

            <div id="dishes-container" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 lg:gap-8">
                <!-- Dishes will be dynamically loaded here -->
            </div>
        </div>
    </section>

    <!-- ABOUT -->
    <section id="about" class="pb-16 sm:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 grid lg:grid-cols-2 gap-8 lg:gap-12 items-center">

            <img class="rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition duration-300 w-full h-64 sm:h-80 lg:h-96 object-cover"
                src="https://images.unsplash.com/photo-1559339352-11d035aa65de" data-aos="fade-right"
                alt="Restaurant Interior" />

            <div data-aos="fade-left">
                <p class="tracking-widest text-xs sm:text-sm mb-4 text-orange-500">ABOUT {{ strtoupper($store->name ?? 'ALAM KITCHEN') }}</p>
                <h2 class="text-2xl sm:text-3xl md:text-4xl mb-4 sm:mb-6 font-semibold">
                    @if($store && $store->about_us)
                    {{ Str::limit($store->about_us, 60, '') }}
                    @else
                    The Health Food For Wealthy Mood
                    @endif
                </h2>
                <p class="opacity-70 mb-6 text-sm sm:text-base leading-relaxed">
                    @if($store && $store->about_us)
                    {{ $store->about_us }}
                    @elseif($store && $store->description)
                    {{ $store->description }}
                    @else
                    Elevating everyday meals into extraordinary experiences with creativity, passion, and the finest
                    ingredients sourced from around the world.
                    @endif
                </p>

                <div class="grid grid-cols-2 sm:flex sm:flex-wrap gap-3 sm:gap-4 mb-6 text-xs sm:text-sm">
                    <span class="bg-gray-200 dark:bg-gray-800 px-3 sm:px-4 py-2 rounded-full text-center">📦 Online
                        Order</span>
                    <span class="bg-gray-200 dark:bg-gray-800 px-3 sm:px-4 py-2 rounded-full text-center">📅 Pre
                        Booking</span>
                    <span class="bg-gray-200 dark:bg-gray-800 px-3 sm:px-4 py-2 rounded-full text-center">✨ Hygiene
                        Place</span>
                    <span class="bg-gray-200 dark:bg-gray-800 px-3 sm:px-4 py-2 rounded-full text-center">🏆 Award
                        Winning</span>
                </div>

                <button onclick="openBookingModal()"
                    class="bg-black dark:bg-white text-white dark:text-black px-6 sm:px-8 py-3 rounded-full hover:scale-105 transition font-semibold">
                    Book Your Table
                </button>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <section class="pb-16 sm:pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 text-center">
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-3xl py-6 sm:py-8 px-4 hover:scale-105 transition shadow-lg"
                data-aos="zoom-in">
                <div class="text-2xl sm:text-3xl font-bold mb-1">50+</div>
                <div class="text-xs sm:text-sm opacity-90">Food Variants</div>
            </div>
            <div class="bg-gradient-to-br from-black to-gray-800 text-white rounded-3xl py-6 sm:py-8 px-4 hover:scale-105 transition shadow-lg"
                data-aos="zoom-in" data-aos-delay="100">
                <div class="text-2xl sm:text-3xl font-bold mb-1">7</div>
                <div class="text-xs sm:text-sm opacity-90">Awards</div>
            </div>
            <div class="bg-gradient-to-br from-orange-500 to-orange-600 text-white rounded-3xl py-6 sm:py-8 px-4 hover:scale-105 transition shadow-lg"
                data-aos="zoom-in" data-aos-delay="200">
                <div class="text-2xl sm:text-3xl font-bold mb-1">125K</div>
                <div class="text-xs sm:text-sm opacity-90">Happy Clients</div>
            </div>
            <div class="bg-gradient-to-br from-black to-gray-800 text-white rounded-3xl py-6 sm:py-8 px-4 hover:scale-105 transition shadow-lg"
                data-aos="zoom-in" data-aos-delay="300">
                <div class="text-2xl sm:text-3xl font-bold mb-1">8</div>
                <div class="text-xs sm:text-sm opacity-90">Branches</div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    @if($faqs->count() > 0)
    <section id="faq" class="pb-16 sm:pb-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6" data-aos="fade-up">
            <div class="text-center mb-10 sm:mb-14">
                <p class="tracking-widest text-xs sm:text-sm mb-3 text-orange-500">FAQ</p>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-semibold">Pertanyaan yang Sering Diajukan</h2>
                <p class="opacity-70 mt-3 text-sm sm:text-base max-w-xl mx-auto">
                    Temukan jawaban untuk pertanyaan yang paling sering ditanyakan tentang kami.
                </p>
            </div>

            <div class="space-y-3">
                @foreach($faqs as $index => $faq)
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden transition hover:shadow-md">
                    <button onclick="toggleFaq({{ $index }})" class="w-full flex justify-between items-center p-5 sm:p-6 text-left">
                        <span class="font-semibold text-sm sm:text-base pr-4">{{ $faq->question }}</span>
                        <svg class="w-5 h-5 flex-shrink-0 text-orange-500 transition-transform duration-300" id="faq-icon-{{ $index }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="faq-answer hidden px-5 sm:px-6 pb-5 sm:pb-6" id="faq-answer-{{ $index }}">
                        <p class="text-sm opacity-70 leading-relaxed">{{ $faq->answer }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- GOOGLE MAPS -->
    @if($store && $store->google_maps_embed)
    <section id="maps" class="pb-16 sm:pb-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6" data-aos="fade-up">
            <div class="text-center mb-10 sm:mb-14">
                <p class="tracking-widest text-xs sm:text-sm mb-3 text-orange-500">LOKASI KAMI</p>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-semibold">Temukan Kami</h2>
            </div>
            <div class="rounded-3xl overflow-hidden shadow-lg border border-gray-100 dark:border-gray-800">
                <div class="aspect-video w-full">
                    {!! $store->google_maps_embed !!}
                </div>
            </div>
            @if($store->google_maps_url)
            <div class="text-center mt-4">
                <a href="{{ $store->google_maps_url }}" target="_blank" rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 text-sm text-orange-500 hover:text-orange-600 font-semibold transition">
                    📍 Buka di Google Maps
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </a>
            </div>
            @endif
        </div>
    </section>
    @endif

    <!-- CONTACT -->
    <section id="contact" class="pb-16 sm:pb-24">
        <div class="max-w-6xl mx-auto px-4 sm:px-6" data-aos="fade-up">
            <div class="text-center mb-10 sm:mb-14">
                <p class="tracking-widest text-xs sm:text-sm mb-3 text-orange-500">HUBUNGI KAMI</p>
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-semibold">Kirim Pesan Kepada Kami</h2>
                <p class="opacity-70 mt-3 text-sm sm:text-base max-w-xl mx-auto">
                    Ada pertanyaan, saran, atau ingin bekerja sama? Isi formulir di bawah dan tim kami akan segera merespons.
                </p>
            </div>

            @if(session('contact_success'))
            <div class="mb-8 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-400 px-5 py-4 rounded-2xl flex items-center gap-3 max-w-2xl mx-auto">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <p class="text-sm font-medium">{{ session('contact_success') }}</p>
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8 lg:gap-12 items-start">

                {{-- Contact Info --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-10 h-10 rounded-2xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Telepon</p>
                                <p class="font-semibold text-sm">{{ $store->phone ?? '+62 812-3456-7890' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-10 h-10 rounded-2xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Email</p>
                                <p class="font-semibold text-sm">{{ $store->email ?? 'hello@alamkitchen.com' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="w-10 h-10 rounded-2xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Jam Operasional</p>
                                <p class="font-semibold text-sm">
                                    @if($store && $store->opening_time && $store->closing_time)
                                    Senin – Minggu: {{ $store->opening_time->format('H:i') }} – {{ $store->closing_time->format('H:i') }}
                                    @else
                                    Senin – Minggu: 10:00 – 23:00
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white dark:bg-gray-900 rounded-3xl p-6 shadow-lg border border-gray-100 dark:border-gray-800">
                        <div class="flex items-top gap-4">
                            <div class="w-10 h-10 rounded-2xl bg-orange-100 dark:bg-orange-900/30 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 mb-0.5">Alamat</p>
                                <p class="font-semibold text-sm leading-relaxed">{{ $store->address ?? 'Jl. Kuliner Nusantara No. 12, Kota Harmoni, Indonesia' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Contact Form --}}
                <div class="lg:col-span-3 bg-white dark:bg-gray-900 rounded-3xl p-6 sm:p-8 shadow-lg border border-gray-100 dark:border-gray-800">
                    <form id="contact-form" method="POST" action="{{ route('contact.store') }}" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium mb-1.5">Nama Lengkap <span class="text-orange-500">*</span></label>
                                <input type="text" name="name" required value="{{ old('name') }}"
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                                    placeholder="Nama Anda">
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1.5">No. Telepon</label>
                                <input type="tel" name="phone" value="{{ old('phone') }}"
                                    class="w-full px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                                    placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Alamat Email <span class="text-orange-500">*</span></label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                                placeholder="email@contoh.com">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Subjek <span class="text-orange-500">*</span></label>
                            <input type="text" name="subject" required value="{{ old('subject') }}"
                                class="w-full px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm"
                                placeholder="Pertanyaan / Saran / Keluhan...">
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1.5">Pesan <span class="text-orange-500">*</span></label>
                            <textarea name="message" rows="5" required
                                class="w-full px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm resize-none"
                                placeholder="Tulis pesan Anda di sini...">{{ old('message') }}</textarea>
                        </div>
                        @if($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300 px-4 py-3 rounded-2xl text-sm">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                        <button type="submit" id="contact-submit-btn"
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white py-3.5 rounded-full font-semibold transition text-sm flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim Pesan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white dark:bg-gray-900 py-12 sm:py-16 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 text-center sm:text-left mb-8">
                <div>
                    <h3 class="font-semibold mb-3 text-lg">{{ $store->name ?? 'Alam Kitchen' }}</h3>
                    <p class="opacity-70 text-sm leading-relaxed">
                        {{ $store->description ?? 'A culinary haven where diverse flavors meet warm hospitality.' }}
                    </p>
                    {{-- Social Media Links --}}
                    @if($store && ($store->instagram || $store->facebook || $store->tiktok || $store->twitter))
                    <div class="flex gap-3 mt-4 justify-center sm:justify-start">
                        @if($store->instagram)
                        <a href="https://instagram.com/{{ $store->instagram }}" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-full bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center text-white hover:scale-110 transition shadow-md" title="Instagram">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z" />
                            </svg>
                        </a>
                        @endif
                        @if($store->facebook)
                        <a href="https://facebook.com/{{ $store->facebook }}" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-full bg-blue-600 flex items-center justify-center text-white hover:scale-110 transition shadow-md" title="Facebook">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                        @endif
                        @if($store->tiktok)
                        <a href="https://tiktok.com/@{{ $store->tiktok }}" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-full bg-black dark:bg-white dark:text-black flex items-center justify-center text-white hover:scale-110 transition shadow-md" title="TikTok">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z" />
                            </svg>
                        </a>
                        @endif
                        @if($store->twitter)
                        <a href="https://x.com/{{ $store->twitter }}" target="_blank" rel="noopener noreferrer"
                            class="w-9 h-9 rounded-full bg-gray-900 dark:bg-gray-100 dark:text-gray-900 flex items-center justify-center text-white hover:scale-110 transition shadow-md" title="X / Twitter">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z" />
                            </svg>
                        </a>
                        @endif
                    </div>
                    @endif
                </div>

                <div>
                    <h3 class="font-semibold mb-3 text-lg">Quick Links</h3>
                    <div class="space-y-2 text-sm">
                        <a href="#home"
                            class="block opacity-70 hover:opacity-100 hover:text-orange-500 transition">Home</a>
                        <a href="#menu"
                            class="block opacity-70 hover:opacity-100 hover:text-orange-500 transition">Menu</a>
                        <a href="#about"
                            class="block opacity-70 hover:opacity-100 hover:text-orange-500 transition">About</a>
                        <a href="#contact"
                            class="block opacity-70 hover:opacity-100 hover:text-orange-500 transition">Contact</a>
                        @if($faqs->count() > 0)
                        <a href="#faq"
                            class="block opacity-70 hover:opacity-100 hover:text-orange-500 transition">FAQ</a>
                        @endif
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold mb-3 text-lg">Alamat</h3>
                    <p class="opacity-70 text-sm leading-relaxed">
                        {{ $store->address ?? 'Jl. Kuliner Nusantara No. 12, Kota Harmoni, Indonesia' }}
                    </p>
                    @if($store && $store->google_maps_url)
                    <a href="{{ $store->google_maps_url }}" target="_blank" rel="noopener noreferrer"
                        class="inline-flex items-center gap-1 text-xs text-orange-500 hover:text-orange-600 mt-2 transition">
                        📍 Lihat di Maps
                    </a>
                    @endif
                </div>

                <div>
                    <h3 class="font-semibold mb-3 text-lg">Kontak</h3>
                    <p class="opacity-70 text-sm">
                        @if($store && $store->email)
                        📧 {{ $store->email }}<br>
                        @endif
                        @if($store && $store->phone)
                        📞 {{ $store->phone }}<br>
                        @endif
                        @if($store && $store->opening_time && $store->closing_time)
                        ⏰ Senin-Minggu: {{ $store->opening_time->format('H:i') }} - {{ $store->closing_time->format('H:i') }}
                        @else
                        ⏰ Senin-Minggu: 10:00 - 23:00
                        @endif
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-8 text-center text-sm opacity-70">
                <p>&copy; {{ date('Y') }} {{ $store->name ?? 'Alam Kitchen' }}. All rights reserved. Made with ❤️</p>
            </div>
        </div>
    </footer>

    {{-- WhatsApp Floating Button --}}
    @if($store && $store->whatsapp_number)
    <a href="https://wa.me/{{ $store->whatsapp_number }}" target="_blank" rel="noopener noreferrer"
        class="fixed bottom-6 right-6 z-40 w-14 h-14 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center shadow-lg shadow-green-500/30 hover:scale-110 transition-all duration-300 group"
        title="Chat via WhatsApp">
        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
        </svg>
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full animate-ping"></span>
        <span class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full"></span>
    </a>
    @endif

    <!-- Overlay for modals -->
    <div id="overlay" class="hidden fixed inset-0 bg-black/30 z-40" onclick="closeAllModals()"></div>

    <script>
        // Initialize AOS
        AOS.init({
            once: true,
            duration: 800,
            easing: 'ease-out'
        });

        // Dishes Data (from database)
        const dishes = @json($menus).map(menu => ({
            id: menu.id,
            name: menu.name,
            description: menu.description || '',
            price: parseFloat(menu.price),
            discountPrice: menu.discount_price ? parseFloat(menu.discount_price) : null,
            image: menu.image ? '/storage/' + menu.image : 'https://images.unsplash.com/photo-1546069901-ba9599a7e63c',
            category: menu.category ? menu.category.name : 'Uncategorized',
            isSpecial: menu.is_special,
        }));

        // Cart Management
        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Load dishes on page load
        function loadDishes() {
            const container = document.getElementById('dishes-container');
            container.innerHTML = '';

            dishes.forEach((dish, index) => {
                const dishCard = `
                    <div class="group bg-white dark:bg-gray-800 rounded-3xl p-5 shadow-lg hover:shadow-2xl hover:-translate-y-2 transition duration-300"
                         data-aos="fade-up"
                         data-aos-delay="${index * 100}">
                        <img class="rounded-2xl mb-4 h-40 sm:h-48 w-full object-cover" src="${dish.image}" alt="${dish.name}" />
                        <div class="mb-2">
                            <span class="text-xs bg-orange-100 dark:bg-orange-900 text-orange-600 dark:text-orange-300 px-2 py-1 rounded-full">
                                ${dish.category}
                            </span>
                        </div>
                        <h3 class="font-semibold mb-2 text-base sm:text-lg">${dish.name}</h3>
                        <p class="text-xs sm:text-sm opacity-70 mb-3 line-clamp-2">${dish.description}</p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-lg sm:text-xl">Rp ${dish.price.toLocaleString('id-ID')}</span>
                            <button
                                onclick="addToCart(${dish.id})"
                                class="border-2 border-black dark:border-white px-3 sm:px-4 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold group-hover:bg-black group-hover:text-white dark:group-hover:bg-white dark:group-hover:text-black transition">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                `;
                container.innerHTML += dishCard;
            });
        }

        // Add to cart
        function addToCart(dishId) {
            const dish = dishes.find(d => d.id === dishId);
            const existingItem = cart.find(item => item.id === dishId);

            if (existingItem) {
                existingItem.quantity += 1;
            } else {
                cart.push({
                    ...dish,
                    quantity: 1
                });
            }

            updateCart();
            saveCart();
            showNotification(`${dish.name} added to cart!`);
        }

        // Remove from cart
        function removeFromCart(dishId) {
            cart = cart.filter(item => item.id !== dishId);
            updateCart();
            saveCart();
        }

        // Update quantity
        function updateQuantity(dishId, change) {
            const item = cart.find(i => i.id === dishId);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) {
                    removeFromCart(dishId);
                } else {
                    updateCart();
                    saveCart();
                }
            }
        }

        // Update cart display
        function updateCart() {
            const cartItems = document.getElementById('cart-items');
            const cartCount = document.getElementById('cart-count');
            const cartTotal = document.getElementById('cart-total');

            if (cart.length === 0) {
                cartItems.innerHTML = '<p class="text-center text-gray-500 py-8">Keranjang kosong</p>';
                cartCount.classList.add('hidden');
                cartTotal.textContent = 'Rp 0';
                return;
            }

            let total = 0;
            let itemCount = 0;
            let html = '<div class="space-y-4">';

            cart.forEach(item => {
                total += item.price * item.quantity;
                itemCount += item.quantity;

                html += `
                    <div class="flex gap-4 items-center bg-gray-50 dark:bg-gray-800 p-4 rounded-2xl">
                        <img src="${item.image}" class="w-20 h-20 rounded-xl object-cover" alt="${item.name}" />
                        <div class="flex-1">
                            <h4 class="font-semibold text-sm mb-1">${item.name}</h4>
                            <p class="text-orange-500 font-bold">Rp ${item.price.toLocaleString('id-ID')}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <button onclick="updateQuantity(${item.id}, -1)" class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center text-sm">-</button>
                                <span class="text-sm font-semibold">${item.quantity}</span>
                                <button onclick="updateQuantity(${item.id}, 1)" class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center text-sm">+</button>
                            </div>
                        </div>
                        <button onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 text-xl">×</button>
                    </div>
                `;
            });

            html += '</div>';
            cartItems.innerHTML = html;
            cartCount.textContent = itemCount;
            cartCount.classList.remove('hidden');
            cartCount.classList.add('cart-badge');
            cartTotal.textContent = `Rp ${total.toLocaleString('id-ID')}`;

            setTimeout(() => {
                cartCount.classList.remove('cart-badge');
            }, 300);
        }

        // Save cart to localStorage
        function saveCart() {
            localStorage.setItem('cart', JSON.stringify(cart));
        }

        // Toggle cart sidebar
        function toggleCart() {
            const sidebar = document.getElementById('cart-sidebar');
            const overlay = document.getElementById('overlay');

            if (sidebar.classList.contains('translate-x-full')) {
                sidebar.classList.remove('translate-x-full');
                overlay.classList.remove('hidden');
            } else {
                sidebar.classList.add('translate-x-full');
                overlay.classList.add('hidden');
            }
        }

        function checkout() {
            if (cart.length === 0) {
                showNotification('Keranjang kosong!', 'error');
                return;
            }
            openCheckoutModal();
        }

        // Checkout Modal
        function openCheckoutModal() {
            const modal = document.getElementById('checkout-modal');
            const overlay = document.getElementById('overlay');
            const content = document.getElementById('checkout-modal-content');

            // Build summary
            const summaryEl = document.getElementById('checkout-summary');
            let summaryHtml = '<p class="font-semibold text-sm mb-2">Ringkasan Pesanan:</p>';
            let total = 0;

            cart.forEach(item => {
                const sub = item.price * item.quantity;
                total += sub;
                summaryHtml += `<div class="flex justify-between text-sm">
                    <span>${item.name} x${item.quantity}</span>
                    <span>Rp ${sub.toLocaleString('id-ID')}</span>
                </div>`;
            });

            summaryHtml += `<div id="checkout-discount-row" class="hidden flex justify-between text-red-500 font-medium text-sm pt-2">
                <span>Diskon</span>
                <span id="checkout-discount-value">- Rp 0</span>
            </div>`;
            summaryHtml += `<div class="flex justify-between font-bold text-sm border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                <span>Total</span>
                <span id="checkout-total-value">Rp ${total.toLocaleString('id-ID')}</span>
            </div>`;
            window.checkoutSubtotal = total;
            document.getElementById('checkout-promo-input').value = '';
            document.getElementById('checkout-promo-msg').classList.add('hidden');
            document.getElementById('checkout-discount-code').value = '';

            summaryEl.innerHTML = summaryHtml;

            // Build items data for hidden field
            const itemsData = cart.map(item => ({
                menu_id: item.id,
                quantity: item.quantity,
                notes: null
            }));

            // We need to create hidden inputs for each item
            // Remove existing dynamic item inputs
            document.querySelectorAll('.checkout-item-input').forEach(el => el.remove());

            const form = document.getElementById('checkout-form');
            itemsData.forEach((item, index) => {
                const menuInput = document.createElement('input');
                menuInput.type = 'hidden';
                menuInput.name = `items[${index}][menu_id]`;
                menuInput.value = item.menu_id;
                menuInput.className = 'checkout-item-input';
                form.appendChild(menuInput);

                const qtyInput = document.createElement('input');
                qtyInput.type = 'hidden';
                qtyInput.name = `items[${index}][quantity]`;
                qtyInput.value = item.quantity;
                qtyInput.className = 'checkout-item-input';
                form.appendChild(qtyInput);
            });

            // Set minimum pickup time to now + 30 min
            const now = new Date();
            now.setMinutes(now.getMinutes() + 30);
            const pad = n => String(n).padStart(2, '0');
            const minTime = `${now.getFullYear()}-${pad(now.getMonth()+1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
            document.querySelector('input[name="pickup_time"]').setAttribute('min', minTime);

            // Close cart sidebar first
            const cartSidebar = document.getElementById('cart-sidebar');
            if (!cartSidebar.classList.contains('translate-x-full')) {
                toggleCart();
            }

            modal.classList.remove('hidden');
            overlay.classList.remove('hidden');

            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeCheckoutModal() {
            const modal = document.getElementById('checkout-modal');
            const overlay = document.getElementById('overlay');
            const content = document.getElementById('checkout-modal-content');

            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                overlay.classList.add('hidden');
            }, 200);
        }

        // On form submission, clear cart from localStorage immediately
        document.getElementById('checkout-form').addEventListener('submit', function() {
            cart = [];
            localStorage.removeItem('cart');
        });

        // Booking Modal
        function openBookingModal() {
            const modal = document.getElementById('booking-modal');
            const overlay = document.getElementById('overlay');
            const content = document.getElementById('booking-modal-content');

            modal.classList.remove('hidden');
            overlay.classList.remove('hidden');

            setTimeout(() => {
                content.classList.remove('scale-95');
                content.classList.add('scale-100');
            }, 10);
        }

        function closeBookingModal() {
            const modal = document.getElementById('booking-modal');
            const overlay = document.getElementById('overlay');
            const content = document.getElementById('booking-modal-content');

            content.classList.remove('scale-100');
            content.classList.add('scale-95');

            setTimeout(() => {
                modal.classList.add('hidden');
                overlay.classList.add('hidden');
            }, 200);
        }

        // ========== Multi-step Booking ==========
        let bookingCart = []; // [{id, name, price, quantity}]

        function goToStep2() {
            // Validate step 1 required fields
            const name = document.getElementById('rsv-name');
            const phone = document.getElementById('rsv-phone');
            const date = document.getElementById('rsv-date');
            const time = document.getElementById('rsv-time');
            const guests = document.getElementById('rsv-guests');
            const table = document.getElementById('rsv-table');

            if (!name.value || !phone.value || !date.value || !time.value || !guests.value || !table.value) {
                // Trigger browser validation
                const form = document.getElementById('booking-form');
                if (!form.reportValidity()) return;
            }

            document.getElementById('booking-step-1').classList.add('hidden');
            document.getElementById('booking-step-2').classList.remove('hidden');
            document.getElementById('booking-title').textContent = 'Pre-Order Menu';
            document.getElementById('booking-subtitle').textContent = 'Step 2 of 2 — Pilih menu untuk dipesan';
            document.getElementById('step-bar-1').classList.add('bg-orange-500');
            document.getElementById('step-bar-2').classList.remove('bg-gray-200', 'dark:bg-gray-700');
            document.getElementById('step-bar-2').classList.add('bg-orange-500');
        }

        function goToStep1() {
            document.getElementById('booking-step-2').classList.add('hidden');
            document.getElementById('booking-step-1').classList.remove('hidden');
            document.getElementById('booking-title').textContent = 'Book a Table';
            document.getElementById('booking-subtitle').textContent = 'Step 1 of 2 — Reservation Details';
            document.getElementById('step-bar-2').classList.remove('bg-orange-500');
            document.getElementById('step-bar-2').classList.add('bg-gray-200', 'dark:bg-gray-700');
        }

        function addBookingItem(id, name, price) {
            const existing = bookingCart.find(i => i.id === id);
            if (existing) {
                existing.quantity += 1;
            } else {
                bookingCart.push({
                    id,
                    name,
                    price,
                    quantity: 1
                });
            }
            renderBookingCart();
        }

        function removeBookingItem(id) {
            bookingCart = bookingCart.filter(i => i.id !== id);
            renderBookingCart();
        }

        function updateBookingQty(id, delta) {
            const item = bookingCart.find(i => i.id === id);
            if (item) {
                item.quantity += delta;
                if (item.quantity <= 0) {
                    removeBookingItem(id);
                    return;
                }
            }
            renderBookingCart();
        }

        function renderBookingCart() {
            const container = document.getElementById('booking-order-items');
            const totalEl = document.getElementById('booking-order-total');
            const totalVal = document.getElementById('booking-total-value');
            const emptyMsg = document.getElementById('booking-empty-msg');
            const inputsContainer = document.getElementById('booking-items-inputs');

            if (bookingCart.length === 0) {
                container.innerHTML = '<p class="text-gray-400 text-center py-2" id="booking-empty-msg">Belum ada item — klik menu di atas untuk menambahkan</p>';
                totalEl.classList.add('hidden');
                inputsContainer.innerHTML = '';
                return;
            }

            let html = '';
            let total = 0;
            let hiddenInputs = '';

            bookingCart.forEach((item, idx) => {
                const sub = item.price * item.quantity;
                total += sub;

                html += `
                    <div class="flex justify-between items-center">
                        <div class="flex-1">
                            <span class="font-medium">${item.name}</span>
                            <span class="text-gray-400 ml-1">× ${item.quantity}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm">Rp ${sub.toLocaleString('id-ID')}</span>
                            <button type="button" onclick="updateBookingQty(${item.id}, -1)"
                                class="w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs hover:bg-red-200">−</button>
                            <button type="button" onclick="updateBookingQty(${item.id}, 1)"
                                class="w-5 h-5 rounded-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center text-xs hover:bg-green-200">+</button>
                            <button type="button" onclick="removeBookingItem(${item.id})"
                                class="text-red-400 hover:text-red-600 text-xs">✕</button>
                        </div>
                    </div>`;

                hiddenInputs += `
                    <input type="hidden" name="items[${idx}][menu_id]" value="${item.id}">
                    <input type="hidden" name="items[${idx}][quantity]" value="${item.quantity}">`;
            });

            container.innerHTML = html;
            totalEl.classList.remove('hidden');
            totalVal.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            inputsContainer.innerHTML = hiddenInputs;

            document.getElementById('booking-promo-section').classList.remove('hidden');
            window.bookingSubtotal = total;

            // Re-apply promo if exists
            const currentCode = document.getElementById('booking-discount-code').value;
            if (currentCode) {
                applyBookingPromo(currentCode);
            } else {
                document.getElementById('booking-order-discount-row').classList.add('hidden');
            }
        }

        // Category filter for booking menu
        function filterBookingMenu(cat) {
            document.querySelectorAll('.booking-cat-btn').forEach(btn => {
                btn.classList.remove('active-cat', 'bg-orange-500', 'text-white', 'border-orange-500');
                if (btn.dataset.cat === cat) {
                    btn.classList.add('active-cat', 'bg-orange-500', 'text-white', 'border-orange-500');
                }
            });

            document.querySelectorAll('.booking-menu-item').forEach(item => {
                if (cat === 'all' || item.dataset.category === cat) {
                    item.classList.remove('hidden');
                } else {
                    item.classList.add('hidden');
                }
            });
        }

        async function validatePromoCode(code, subtotal) {
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
                return await response.json();
            } catch (error) {
                return {
                    error: 'Terjadi kesalahan jaringan.'
                };
            }
        }

        async function applyCheckoutPromo() {
            const input = document.getElementById('checkout-promo-input');
            const msgEl = document.getElementById('checkout-promo-msg');
            const codeEl = document.getElementById('checkout-discount-code');
            const rowEl = document.getElementById('checkout-discount-row');
            const valEl = document.getElementById('checkout-discount-value');
            const totalEl = document.getElementById('checkout-total-value');

            const code = input.value.trim();
            if (!code) return;

            msgEl.classList.remove('hidden');
            msgEl.className = 'text-xs mt-2 text-gray-500';
            msgEl.textContent = 'Memvalidasi...';

            const data = await validatePromoCode(code, window.checkoutSubtotal);

            if (data.error) {
                msgEl.className = 'text-xs mt-2 text-red-500';
                msgEl.textContent = data.error;
                codeEl.value = '';
                rowEl.classList.add('hidden');
                totalEl.textContent = `Rp ${window.checkoutSubtotal.toLocaleString('id-ID')}`;
            } else if (data.success) {
                msgEl.className = 'text-xs mt-2 text-green-500';
                msgEl.textContent = data.message;
                codeEl.value = data.discount.code;

                const discAmt = data.discount.calculated_discount;
                rowEl.classList.remove('hidden');
                valEl.textContent = `- Rp ${discAmt.toLocaleString('id-ID')}`;

                const finalTotal = window.checkoutSubtotal - discAmt;
                totalEl.textContent = `Rp ${finalTotal.toLocaleString('id-ID')}`;
            }
        }

        async function applyBookingPromo(existingCode = null) {
            const input = document.getElementById('booking-promo-input');
            const msgEl = document.getElementById('booking-promo-msg');
            const codeEl = document.getElementById('booking-discount-code');
            const rowEl = document.getElementById('booking-order-discount-row');
            const valEl = document.getElementById('booking-discount-value');
            const totalEl = document.getElementById('booking-total-value');

            const code = existingCode || input.value.trim();
            if (!code) return;

            if (!existingCode) {
                msgEl.classList.remove('hidden');
                msgEl.className = 'text-xs mt-1 text-gray-500';
                msgEl.textContent = 'Memvalidasi...';
            }

            const data = await validatePromoCode(code, window.bookingSubtotal);

            if (data.error) {
                msgEl.className = 'text-xs mt-1 text-red-500';
                msgEl.textContent = data.error;
                codeEl.value = '';
                rowEl.classList.add('hidden');
                totalEl.textContent = `Rp ${window.bookingSubtotal.toLocaleString('id-ID')}`;
            } else if (data.success) {
                msgEl.className = 'text-xs mt-1 text-green-500';
                msgEl.textContent = data.message;
                codeEl.value = data.discount.code;

                const discAmt = data.discount.calculated_discount;
                rowEl.classList.remove('hidden');
                valEl.textContent = `- Rp ${discAmt.toLocaleString('id-ID')}`;

                const finalTotal = window.bookingSubtotal - discAmt;
                totalEl.textContent = `Rp ${finalTotal.toLocaleString('id-ID')}`;
            }
        }

        // Auto-open modal on validation error or show success
        @if(session('booking_error') || $errors->any())
        document.addEventListener('DOMContentLoaded', function() {
            openBookingModal();
        });
        @endif

        @if(session('booking_success'))
        document.addEventListener('DOMContentLoaded', function() {
            showNotification('{{ session('
                booking_success ') }}');
        });
        @endif

        // FAQ Accordion Toggle
        function toggleFaq(index) {
            const answer = document.getElementById('faq-answer-' + index);
            const icon = document.getElementById('faq-icon-' + index);

            if (answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                answer.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        // Dark Mode Toggle
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('darkMode', isDark);

            const icon = document.getElementById('dark-mode-icon');
            icon.textContent = isDark ? '☀️' : '🌙';
        }

        // Load dark mode preference
        function loadDarkMode() {
            const isDark = localStorage.getItem('darkMode') === 'true';
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.getElementById('dark-mode-icon').textContent = '☀️';
            }
        }

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            menu.classList.toggle('hidden');
        }

        // Close all modals
        function closeAllModals() {
            closeBookingModal();
            closeCheckoutModal();
            const cartSidebar = document.getElementById('cart-sidebar');
            if (!cartSidebar.classList.contains('translate-x-full')) {
                toggleCart();
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const notification = document.getElementById('notification');
            const messageEl = document.getElementById('notification-message');

            messageEl.textContent = message;

            // Set color based on type
            notification.className = `fixed top-24 right-4 px-6 py-4 rounded-2xl shadow-lg z-50 animate-slide-in`;
            if (type === 'success') {
                notification.classList.add('bg-green-500', 'text-white');
            } else if (type === 'error') {
                notification.classList.add('bg-red-500', 'text-white');
            } else {
                notification.classList.add('bg-blue-500', 'text-white');
            }

            notification.classList.remove('hidden');

            setTimeout(() => {
                notification.classList.add('hidden');
            }, 3000);
        }

        // Search functionality
        function searchDish() {
            const query = document.getElementById('search-input').value.toLowerCase();
            if (!query) {
                showNotification('Please enter a search term', 'info');
                return;
            }

            const results = dishes.filter(dish =>
                dish.name.toLowerCase().includes(query) ||
                dish.description.toLowerCase().includes(query) ||
                dish.category.toLowerCase().includes(query)
            );

            if (results.length > 0) {
                showNotification(`Found ${results.length} dish(es)!`);
                // Scroll to menu section
                document.getElementById('menu').scrollIntoView({
                    behavior: 'smooth'
                });
            } else {
                showNotification('No dishes found!', 'error');
            }
        }

        // Scroll dishes (for carousel effect)
        function scrollDishes(direction) {
            const container = document.getElementById('dishes-container');
            const scrollAmount = 300;

            if (direction === 'left') {
                container.scrollBy({
                    left: -scrollAmount,
                    behavior: 'smooth'
                });
            } else {
                container.scrollBy({
                    left: scrollAmount,
                    behavior: 'smooth'
                });
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadDishes();
            updateCart();
            loadDarkMode();
        });

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    // Close mobile menu if open
                    document.getElementById('mobile-menu').classList.add('hidden');
                }
            });
        });
    </script>
    @stack('scripts')
</body>

</html>