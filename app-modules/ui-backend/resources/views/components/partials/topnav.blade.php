<!-- Topbar -->
<header class="fixed top-0 left-0 z-30 flex items-center w-full h-16 bg-white shadow-sm">
    <!-- Left section with logo -->
    <div class="flex items-center pl-4 lg:ml-16" :class="{'lg:ml-64': sidebarOpen, 'lg:ml-16': !sidebarOpen}">
        <!-- Mobile menu button -->
        <button @click="mobileMenuOpen = true" class="p-2 text-gray-700 rounded-md hover:bg-gray-100 lg:hidden">
            <i class="fas fa-bars text-lg"></i>
        </button>

        <!-- Desktop sidebar toggle button - always visible -->
        <button @click="toggleSidebar()" class="p-2 text-gray-700 rounded-md hover:bg-gray-100 hidden lg:block">
            <i class="fas fa-bars text-lg"></i>
        </button>

        <!-- Logo and app name -->
        @if(config('app.logo'))
            <img src="{{ asset(config('app.logo')) }}" alt="Logo" class="w-8 h-8 ml-2">
        @else
            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold ml-2">E</div>
        @endif
        <span class="ml-2 text-xl font-semibold">{{ config('app.name', 'Englishing.org') }}</span>
    </div>
    <div class="flex items-center ml-auto">
        <div class="relative hidden md:block">
            <input type="text" placeholder="Search..." class="w-64 py-2 pl-10 pr-4 text-sm bg-gray-100 border border-transparent rounded-md focus:outline-none focus:border-gray-300">
            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                <i class="fas fa-search text-gray-400"></i>
            </div>
        </div>
        <button class="p-2 ml-4 text-gray-500 rounded-md hover:bg-gray-100">
            <i class="fas fa-bell"></i>
        </button>
        <button class="p-2 ml-2 text-gray-500 rounded-md hover:bg-gray-100">
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
                    <span class="ml-2 text-sm font-medium hidden md:block">{{ auth()->user()->name }}</span>
                @else
                    <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center text-gray-600 font-bold">
                        G
                    </div>
                    <span class="ml-2 text-sm font-medium hidden md:block">Guest</span>
                @endif
                <i class="ml-1 fas fa-chevron-down text-xs hidden md:block"></i>
            </button>
            <div x-cloak x-show="profileOpen" @click.away="profileOpen = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    <i class="fas fa-user mr-2"></i> Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-sign-out-alt mr-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>
