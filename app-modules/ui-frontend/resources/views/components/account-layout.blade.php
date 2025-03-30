<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Englishing.org') }} - {{ __('Account') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Miriam+Libre:wght@400;700&display=swap" rel="stylesheet">

    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
    @stack('styles')
</head>
<body class="font-['Miriam_Libre'] antialiased h-full bg-gray-50 dark:bg-gray-900" x-data="{ darkMode: localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches), sidebarOpen: window.innerWidth >= 768 }">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation (includes header) -->
        <x-ui-frontend::partials.nav />

        <!-- Main Content with Sidebar -->
        <div class="flex-grow flex flex-col md:flex-row">
            <!-- Mobile Sidebar Toggle -->
            <div class="md:hidden p-4 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                <button @click="sidebarOpen = !sidebarOpen" type="button" class="w-full flex items-center justify-between px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <span>{{ __('Account Menu') }}</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        <path x-show="sidebarOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                    </svg>
                </button>
            </div>

            <!-- Sidebar -->
            <div x-show="sidebarOpen || window.innerWidth >= 768" @click.away="if(window.innerWidth < 768) sidebarOpen = false" class="md:w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 p-4 md:p-6 absolute md:relative z-10 w-64 min-h-screen md:min-h-0">
                <div class="mb-8">
                    <div class="flex items-center mb-4">
                        <img class="h-12 w-12 rounded-full object-cover mr-3" src="{{ auth()->user()->profile_photo_url ?? asset('img/default-avatar.png') }}" alt="{{ auth()->user()->name ?? 'User' }}">
                        <div>
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">{{ auth()->user()->name ?? 'User' }}</h2>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                        </div>
                    </div>
                </div>

                <nav class="space-y-1">
                    <h3 class="px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Account') }}
                    </h3>
                    <a href="{{ route('ui-frontend::account.dashboard') }}" class="{{ request()->routeIs('ui-frontend::account.dashboard') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('ui-frontend::account.dashboard') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        {{ __('Dashboard') }}
                    </a>

                    <a href="{{ route('ui-frontend::account.profile') }}" class="{{ request()->routeIs('ui-frontend::account.profile') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('ui-frontend::account.profile') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ __('Profile') }}
                    </a>

                    <h3 class="mt-6 px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Learning') }}
                    </h3>

                    <a href="{{ route('ui-frontend::account.bookmarks') }}" class="{{ request()->routeIs('ui-frontend::account.bookmarks') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('ui-frontend::account.bookmarks') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" />
                        </svg>
                        {{ __('Bookmarks') }}
                    </a>

                    <a href="{{ route('ui-frontend::account.liked') }}" class="{{ request()->routeIs('ui-frontend::account.liked') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('ui-frontend::account.liked') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        {{ __('Liked Content') }}
                    </a>

                    <a href="{{ route('ui-frontend::account.completed') }}" class="{{ request()->routeIs('ui-frontend::account.completed') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('ui-frontend::account.completed') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ __('Completed') }}
                    </a>

                    <a href="{{ route('ui-frontend::account.courses') }}" class="{{ request()->routeIs('ui-frontend::account.courses') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('ui-frontend::account.courses') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        {{ __('My Courses') }}
                    </a>

                    <h3 class="mt-6 px-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                        {{ __('Settings') }}
                    </h3>

                    <a href="{{ route('ui-frontend::account.settings') }}" class="{{ request()->routeIs('ui-frontend::account.settings') ? 'bg-indigo-50 dark:bg-indigo-900 text-indigo-700 dark:text-indigo-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <svg class="mr-3 h-5 w-5 {{ request()->routeIs('ui-frontend::account.settings') ? 'text-indigo-500 dark:text-indigo-400' : 'text-gray-400 dark:text-gray-500 group-hover:text-gray-500 dark:group-hover:text-gray-400' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        {{ __('Settings') }}
                    </a>
                </nav>
            </div>

            <!-- Content Area -->
            <main class="flex-1 p-4 md:p-8">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-4 md:p-6">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <!-- Footer -->
        <x-ui-frontend::partials.footer />
    </div>

    <!-- Scripts -->
    <x-ui-frontend::partials.scripts />
    @livewireScripts
    @stack('scripts')

    <!-- Flowbite JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>

    <!-- Account Layout Scripts -->
    <script>
        // Ensure sidebar state is maintained on resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 768) {
                document.querySelector('[x-data]').__x.$data.sidebarOpen = true;
            }
        });

        // Initialize sidebar state on page load
        window.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth >= 768) {
                document.querySelector('[x-data]').__x.$data.sidebarOpen = true;
            }
        });
    </script>
</body>
</html>
