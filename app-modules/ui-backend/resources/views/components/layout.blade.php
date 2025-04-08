<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Englishing.org') }} - Admin</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Miriam+Libre:wght@400;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('vendor/jquery/jquery.js') }}"></script>

    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}">

    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.3/css/fixedHeader.dataTables.min.css">

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @stack('styles')

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition {
            transition: width 0.3s ease;
        }
    </style>
</head>
<body class="font-['Miriam_Libre'] antialiased h-full bg-gray-100 dark:bg-gray-900 dark:text-white"
      x-data="{
        sidebarOpen: true,
        mobileMenuOpen: false,
        darkMode: localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
        init() {
          // Load the sidebar state from localStorage if it exists
          const saved = localStorage.getItem('sidebarOpen');
          if (saved !== null) {
            this.sidebarOpen = JSON.parse(saved);
          }
          
          // Apply dark mode class if needed
          if (this.darkMode) {
            document.documentElement.classList.add('dark');
          } else {
            document.documentElement.classList.remove('dark');
          }
        },
        toggleSidebar() {
          this.sidebarOpen = !this.sidebarOpen;
          // Save the sidebar state in localStorage
          localStorage.setItem('sidebarOpen', JSON.stringify(this.sidebarOpen));
        }
      }"
      x-init="init()">
    
    <!-- Toast Component -->
    <x-toast />
    
    <!-- Mobile Navigation -->
    <div x-cloak x-show="mobileMenuOpen" class="fixed inset-0 z-50 bg-gray-900 bg-opacity-50 lg:hidden"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg">
            <div class="flex items-center justify-between p-4 border-b">
                <div class="flex items-center">
                    @if(config('app.logo'))
                        <img src="{{ asset(config('app.logo')) }}" alt="Logo" class="w-8 h-8 mr-2">
                    @else
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold mr-2">E</div>
                    @endif
                    <span class="text-xl font-semibold">{{ config('app.name', 'Englishing.org') }}</span>
                </div>
                <button @click="mobileMenuOpen = false" class="p-2 text-gray-500 rounded-md hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="p-4">
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('ui-backend::index') }}" class="flex items-center p-2 text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="fas fa-home w-6"></i>
                            <span class="ml-3">Dashboard</span>
                        </a>
                    </li>
                    @role('admin')
                    <li>
                        <a href="#" class="flex items-center p-2 text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="fas fa-users w-6"></i>
                            <span class="ml-3">Users</span>
                        </a>
                    </li>
                    @endrole
                    <li>
                        <a href="#" class="flex items-center p-2 text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="fas fa-newspaper w-6"></i>
                            <span class="ml-3">Articles</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center p-2 text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="fas fa-comment w-6"></i>
                            <span class="ml-3">Sentences</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center p-2 text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="fas fa-book w-6"></i>
                            <span class="ml-3">Words</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="flex items-center p-2 text-gray-700 rounded-md hover:bg-gray-100">
                            <i class="fas fa-cog w-6"></i>
                            <span class="ml-3">Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Topbar -->
    <x-ui-backend::partials.topnav />

    <!-- Desktop Sidebar -->
    <x-ui-backend::partials.sidebar />

    <!-- Main Content Area -->
    <main x-cloak class="pt-16 transition-all duration-300 dark:bg-gray-900" :class="{'lg:pl-64': sidebarOpen, 'lg:pl-16': !sidebarOpen}">
        <div class="p-4 dark:text-gray-200">
            @if(isset($header))
                <div class="mb-6">
                    {{ $header }}
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>

    <!-- Scripts -->
    <x-ui-backend::partials.scripts />
    @stack('scripts')

    <!-- Livewire Scripts -->
    @livewireScripts

    <!-- Flowbite JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>
</html>
