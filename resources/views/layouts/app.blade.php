<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Alam Kitchen | @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
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

<body class="bg-gray-50 font-sans dark:bg-gray-950 text-gray-800 dark:text-gray-200 transition duration-300">

    <div class="flex min-h-screen">

        @include('layouts.partials.sidebar')



        <!-- MAIN CONTENT -->
        <main class="flex-1 flex flex-col min-h-screen">

            <!-- TOP NAVBAR -->
            @include('layouts.partials.header')
            <!-- DASHBOARD CONTENT -->
            <div class="flex-1 p-4 sm:p-8">

                <!-- Page Header -->
                @yield('content')

            </div>
        </main>
    </div>

    <script>
        // Dark Mode Toggle
        function toggleDarkMode() {
            document.documentElement.classList.toggle('dark');
            const isDark = document.documentElement.classList.contains('dark');
            localStorage.setItem('darkMode', isDark);
            document.getElementById('dark-mode-icon').textContent = isDark ? '☀️' : '🌙';
        }

        // Load Dark Mode Preference
        function loadDarkMode() {
            const isDark = localStorage.getItem('darkMode') === 'true';
            if (isDark) {
                document.documentElement.classList.add('dark');
                document.getElementById('dark-mode-icon').textContent = '☀️';
            }
        }

        // Toggle Mobile Sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }

        // Toggle Dropdown
        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const allDropdowns = ['inbox-dropdown', 'notification-dropdown'];
            
            allDropdowns.forEach(d => {
                if (d !== id) {
                    document.getElementById(d).classList.add('hidden');
                }
            });
            
            dropdown.classList.toggle('hidden');
        }

        // Mark all notifications as read
        function markAllNotificationsAsRead() {
            fetch('{{ route("notifications.markAllRead") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI: hide badges and mark items as read
                    const badges = document.querySelectorAll('#notification-dropdown-container .bg-red-500');
                    badges.forEach(b => b.classList.add('hidden'));
                    
                    const unreadItems = document.querySelectorAll('#notification-dropdown .bg-emerald-50\\/30');
                    unreadItems.forEach(i => i.classList.remove('bg-emerald-50/30', 'dark:bg-emerald-900/10'));
                }
            })
            .catch(error => console.error('Error marking notifications as read:', error));
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            loadDarkMode();

            // Close dropdowns when clicking outside
            window.addEventListener('click', function(e) {
                if (!document.getElementById('inbox-dropdown-container').contains(e.target)) {
                    document.getElementById('inbox-dropdown').classList.add('hidden');
                }
                if (!document.getElementById('notification-dropdown-container').contains(e.target)) {
                    document.getElementById('notification-dropdown').classList.add('hidden');
                }
            });
        });
    </script>
    @stack('scripts')

</body>

</html>
