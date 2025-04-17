<!-- Topbar -->
<header class="fixed top-0 left-0 z-30 flex items-center w-full h-16 bg-white dark:bg-gray-800 shadow-sm">
    <!-- Left section with logo -->
    <div class="flex items-center pl-4 lg:ml-16" :class="{'lg:ml-64': sidebarOpen, 'lg:ml-16': !sidebarOpen}">
        <!-- Mobile menu button -->
        <button @click="mobileMenuOpen = true" class="p-2 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 lg:hidden">
            <i class="fas fa-bars text-lg"></i>
        </button>

        <!-- Desktop sidebar toggle button - always visible -->
        <button @click="toggleSidebar()" class="p-2 text-gray-700 dark:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 hidden lg:block">
            <i class="fas fa-bars text-lg"></i>
        </button>

        <!-- Logo and app name -->
        @if(config('app.logo'))
            <img src="{{ asset(config('app.logo')) }}" alt="Logo" class="w-8 h-8 ml-2">
        @else
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold ml-2">E</div>
        @endif
        <span class="ml-2 text-xl font-semibold dark:text-white">{{ config('app.name', 'Englishing.org') }}</span>
    </div>
    <div class="flex items-center ml-auto">
        <div class="relative hidden md:block">
            <input type="text" placeholder="Search..." class="w-64 py-2 pl-10 pr-4 text-sm bg-gray-100 dark:bg-gray-700 dark:text-gray-200 border border-transparent rounded-md focus:outline-none focus:border-gray-300 dark:focus:border-gray-600">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-gray-400"></i>
            </div>
        </div>
        <!-- Theme Toggle -->
        <button @click="darkMode = !darkMode; localStorage.setItem('color-theme', darkMode ? 'dark' : 'light'); document.documentElement.classList.toggle('dark')" type="button" class="p-2 ml-4 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" x-show="!darkMode">
                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
            </svg>
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" x-show="darkMode">
                <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
            </svg>
        </button>
        <button class="p-2 ml-4 text-gray-500 dark:text-gray-400 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            <i class="fas fa-bell"></i>
        </button>
        <button class="p-2 ml-2 text-gray-500 dark:text-gray-400 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700">
            <i class="fas fa-envelope"></i>
        </button>
        <div class="relative ml-4" x-data="{profileOpen: false}">
            <button @click="profileOpen = !profileOpen" class="flex items-center">
                @if(auth()->check())
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset('storage/' . auth()->user()->profile_photo_path) }}" alt="User" class="w-8 h-8 rounded-full">
                    @else
                        <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </div>
                    @endif
                    <span class="ml-2 text-sm font-medium hidden md:block dark:text-gray-200">{{ auth()->user()->name }}</span>
                @else
                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                        G
                    </div>
                    <span class="ml-2 text-sm font-medium hidden md:block dark:text-gray-200">Guest</span>
                @endif
                <i class="ml-1 fas fa-chevron-down text-xs hidden md:block dark:text-gray-200"></i>
            </button>
            <div x-cloak x-show="profileOpen" @click.away="profileOpen = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-700 rounded-md shadow-lg py-1 z-50">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-600">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
