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
            <h1 class="tracking-[0.3em] font-semibold text-xs sm:text-sm md:text-base">Alam Kitchen</h1>

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

                <button type="submit"
                    class="w-full bg-orange-500 text-white py-3 rounded-full hover:bg-orange-600 transition font-semibold">
                    Pesan Sekarang
                </button>
            </form>
        </div>
    </div>

    <!-- Booking Modal -->
    <div id="booking-modal"
        class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-900 rounded-3xl max-w-md w-full p-6 sm:p-8 transform scale-95 transition-transform duration-300"
            id="booking-modal-content">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-semibold">Book a Table</h3>
                <button onclick="closeBookingModal()" class="text-2xl hover:text-orange-500 transition">&times;</button>
            </div>

            <form id="booking-form" class="space-y-4">
                <div>
                    <label class="block text-sm mb-2">Full Name</label>
                    <input type="text" id="booking-name" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm mb-2">Email</label>
                    <input type="email" id="booking-email" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div>
                    <label class="block text-sm mb-2">Phone Number</label>
                    <input type="tel" id="booking-phone" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm mb-2">Date</label>
                        <input type="date" id="booking-date" required
                            class="w-full px-4 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                    <div>
                        <label class="block text-sm mb-2">Time</label>
                        <input type="time" id="booking-time" required
                            class="w-full px-4 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm mb-2">Number of Guests</label>
                    <select id="booking-guests" required
                        class="w-full px-4 py-3 rounded-full border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500">
                        <option value="">Select guests</option>
                        <option value="1">1 Guest</option>
                        <option value="2">2 Guests</option>
                        <option value="3">3 Guests</option>
                        <option value="4">4 Guests</option>
                        <option value="5">5 Guests</option>
                        <option value="6">6+ Guests</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-2">Special Requests (Optional)</label>
                    <textarea id="booking-requests" rows="3"
                        class="w-full px-4 py-3 rounded-2xl border border-gray-300 dark:border-gray-700 bg-transparent focus:outline-none focus:ring-2 focus:ring-orange-500"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-black dark:bg-white text-white dark:text-black py-3 rounded-full hover:scale-105 transition font-semibold">
                    Confirm Booking
                </button>
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
                <p class="tracking-widest text-xs sm:text-sm mb-4 text-orange-500">ABOUT H&H KITCHEN</p>
                <h2 class="text-2xl sm:text-3xl md:text-4xl mb-4 sm:mb-6 font-semibold">The Health Food For Wealthy
                    Mood</h2>
                <p class="opacity-70 mb-6 text-sm sm:text-base leading-relaxed">
                    Elevating everyday meals into extraordinary experiences with creativity, passion, and the finest
                    ingredients sourced from around the world.
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

    <!-- NEWSLETTER -->
    <section id="contact" class="pb-16 sm:pb-24">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center" data-aos="fade-up">
            <h2 class="text-2xl sm:text-3xl md:text-4xl mb-4 font-semibold">Subscribe to Our Newsletter</h2>
            <p class="opacity-70 mb-6 sm:mb-8 text-sm sm:text-base">
                Find the tastiest dishes and get exclusive offers delivered to your inbox.
            </p>

            <form id="newsletter-form" class="flex flex-col sm:flex-row justify-center gap-3 max-w-md mx-auto">
                <input type="email" id="newsletter-email" required
                    class="bg-white dark:bg-gray-800 px-5 py-3 rounded-full w-full sm:flex-1 focus:outline-none focus:ring-2 focus:ring-orange-500 border border-gray-300 dark:border-gray-700"
                    placeholder="Your email address" />
                <button type="submit"
                    class="bg-orange-500 text-white px-6 sm:px-8 py-3 rounded-full hover:bg-orange-600 transition font-semibold whitespace-nowrap">
                    Subscribe
                </button>
            </form>
        </div>
    </section>

    <!-- FOOTER -->
    <footer class="bg-white dark:bg-gray-900 py-12 sm:py-16 border-t border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 text-center sm:text-left mb-8">
                <div>
                    <h3 class="font-semibold mb-3 text-lg">H&H Kitchen</h3>
                    <p class="opacity-70 text-sm leading-relaxed">
                        A culinary haven where diverse flavors meet warm hospitality.
                    </p>
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
                    </div>
                </div>

                <div>
                    <h3 class="font-semibold mb-3 text-lg">Address</h3>
                    <p class="opacity-70 text-sm">
                        197 Pomeroy Ave<br>
                        California, CA 94025<br>
                        United States
                    </p>
                </div>

                <div>
                    <h3 class="font-semibold mb-3 text-lg">Contact</h3>
                    <p class="opacity-70 text-sm">
                        📧 hello@hhkitchen.com<br>
                        📞 +1 (555) 123-4567<br>
                        ⏰ Mon-Sun: 10AM - 11PM
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-8 text-center text-sm opacity-70">
                <p>&copy; 2024 H&H Kitchen. All rights reserved. Made with ❤️</p>
            </div>
        </div>
    </footer>

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

            summaryHtml += `<div class="flex justify-between font-bold text-sm border-t border-gray-200 dark:border-gray-700 pt-2 mt-2">
                <span>Total</span>
                <span>Rp ${total.toLocaleString('id-ID')}</span>
            </div>`;

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

            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('booking-date').setAttribute('min', today);
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

        // Booking Form Submit
        document.getElementById('booking-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = {
                name: document.getElementById('booking-name').value,
                email: document.getElementById('booking-email').value,
                phone: document.getElementById('booking-phone').value,
                date: document.getElementById('booking-date').value,
                time: document.getElementById('booking-time').value,
                guests: document.getElementById('booking-guests').value,
                requests: document.getElementById('booking-requests').value
            };

            // Save booking to localStorage
            let bookings = JSON.parse(localStorage.getItem('bookings')) || [];
            bookings.push({
                ...formData,
                id: Date.now(),
                createdAt: new Date().toISOString()
            });
            localStorage.setItem('bookings', JSON.stringify(bookings));

            closeBookingModal();
            showNotification(`Booking confirmed for ${formData.name} on ${formData.date}!`);

            // Reset form
            this.reset();
        });

        // Newsletter Form
        document.getElementById('newsletter-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('newsletter-email').value;

            // Save email to localStorage
            let subscribers = JSON.parse(localStorage.getItem('subscribers')) || [];
            if (!subscribers.includes(email)) {
                subscribers.push(email);
                localStorage.setItem('subscribers', JSON.stringify(subscribers));
                showNotification('Successfully subscribed to newsletter!');
                this.reset();
            } else {
                showNotification('You are already subscribed!', 'info');
            }
        });

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
