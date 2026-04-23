<header
    class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-800 px-4 sm:px-8 py-4 sticky top-0 z-30">
    <div class="flex items-start justify-between gap-4">

        <!-- Mobile Menu Button -->
        <button onclick="toggleSidebar()"
            class="lg:hidden p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        <!-- Right Side Icons & Profile -->
        <div class="flex items-center gap-3 ml-auto"> <!-- Inbox/Email Dropdown -->
            <div class="relative" id="inbox-dropdown-container">
                <button onclick="toggleDropdown('inbox-dropdown')"
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                        </path>
                    </svg>
                    @if($unreadContactsCount > 0)
                    <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white dark:border-gray-900">
                        {{ $unreadContactsCount > 9 ? '9+' : $unreadContactsCount }}
                    </span>
                    @endif
                </button>

                <!-- Inbox Dropdown Content -->
                <div id="inbox-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-semibold text-sm">Pesan Masuk</h3>
                        @php
                        $contactRoute = '#';
                        if (Auth::user()->hasRole('super_admin')) $contactRoute = route('super_admin.contact.index');
                        elseif (Auth::user()->hasRole('admin')) $contactRoute = route('admin.contact.index');
                        @endphp
                        <a href="{{ $contactRoute }}" class="text-xs text-emerald-500 hover:underline">Lihat Semua</a>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($recentContacts as $contact)
                        @php
                        $contactShowRoute = '#';
                        if (Auth::user()->hasRole('super_admin')) $contactShowRoute = route('super_admin.contact.show', $contact);
                        elseif (Auth::user()->hasRole('admin')) $contactShowRoute = route('admin.contact.show', $contact);
                        @endphp
                        <a href="{{ $contactShowRoute }}" class="block p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition border-b border-gray-50 dark:border-gray-700">
                            <div class="flex justify-between items-start mb-1">
                                <span class="font-semibold text-xs text-emerald-600">{{ $contact->name }}</span>
                                <span class="text-[10px] text-gray-400">{{ $contact->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-xs font-medium truncate">{{ $contact->subject }}</p>
                            <p class="text-[10px] text-gray-500 line-clamp-1 mt-0.5">{{ $contact->message }}</p>
                        </a>
                        @empty
                        <div class="p-8 text-center">
                            <p class="text-gray-400 text-sm">Tidak ada pesan baru</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Notification Dropdown -->
            <div class="relative" id="notification-dropdown-container">
                <button onclick="toggleDropdown('notification-dropdown')"
                    class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition relative">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                        </path>
                    </svg>
                    @if($unreadNotificationsCount > 0)
                    <span class="absolute top-1 right-1 w-4 h-4 bg-red-500 text-white text-[10px] flex items-center justify-center rounded-full border-2 border-white dark:border-gray-900">
                        {{ $unreadNotificationsCount > 9 ? '9+' : $unreadNotificationsCount }}
                    </span>
                    @endif
                </button>

                <!-- Notification Dropdown Content -->
                <div id="notification-dropdown" class="hidden absolute right-0 mt-2 w-80 bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden z-50">
                    <div class="p-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="font-semibold text-sm">Notifikasi</h3>
                        <button onclick="markAllNotificationsAsRead()" class="text-xs text-emerald-500 hover:underline">Tandai Dibaca</button>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse($recentNotifications as $notification)
                        @php
                        $notifTargetRoute = '#';
                        if ($notification->data['type'] === 'order') {
                        if (Auth::user()->hasRole('super_admin')) $notifTargetRoute = route('super_admin.orders.index');
                        elseif (Auth::user()->hasRole('admin')) $notifTargetRoute = route('admin.orders.index');
                        elseif (Auth::user()->hasRole('kasir')) $notifTargetRoute = route('kasir.orders.index');
                        } else {
                        if (Auth::user()->hasRole('super_admin')) $notifTargetRoute = route('super_admin.reservasi.index');
                        elseif (Auth::user()->hasRole('admin')) $notifTargetRoute = route('admin.reservasi.index');
                        elseif (Auth::user()->hasRole('kasir')) $notifTargetRoute = route('kasir.reservasi.index');
                        }
                        @endphp
                        <a href="{{ $notifTargetRoute }}" class="block p-4 border-b border-gray-50 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ !$notification->read_at ? 'bg-emerald-50/30 dark:bg-emerald-900/10' : '' }}">
                            <div class="flex gap-3">
                                <div class="w-8 h-8 rounded-full flex-shrink-0 flex items-center justify-center {{ $notification->data['type'] === 'order' ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600' }}">
                                    @if($notification->data['type'] === 'order')
                                    🛒
                                    @else
                                    📅
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-medium">{{ $notification->data['message'] }}</p>
                                    <span class="text-[10px] text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center">
                            <p class="text-gray-400 text-sm">Tidak ada notifikasi</p>
                        </div>
                        @endforelse
                    </div>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700/50 text-center">
                        @php
                        $allNotifRoute = '#';
                        if (Auth::user()->hasRole('super_admin')) $allNotifRoute = route('super_admin.orders.index');
                        elseif (Auth::user()->hasRole('admin')) $allNotifRoute = route('admin.orders.index');
                        elseif (Auth::user()->hasRole('kasir')) $allNotifRoute = route('kasir.orders.index');
                        @endphp
                        <a href="{{ $allNotifRoute }}" class="text-xs font-medium text-emerald-600 hover:underline">Lihat Semua</a>
                    </div>
                </div>
            </div>

            <!-- Profile -->
            <div class="flex items-center gap-3 pl-3 border-l border-gray-200 dark:border-gray-700">
                <div class="hidden sm:block text-right">
                    <p class="text-sm font-semibold">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-500">{{ Auth::user()->email ?? '' }}</p>
                </div>
                @php
                $initials = collect(explode(' ', Auth::user()->name ?? 'U'))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->join('');
                @endphp
                <div
                    class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-emerald-400 rounded-full flex items-center justify-center text-white font-semibold">
                    {{ $initials }}
                </div>
            </div>
        </div>
    </div>
</header>