<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100 dark:bg-gray-900">
<x-ui-backend::partials._head />
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
    <x-ui-backend::partials._mobile-nav />

    <!-- Topbar -->
    <x-ui-backend::partials._topnav />

    <!-- Desktop Sidebar -->
    <x-ui-backend::partials._sidebar />

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
    <x-ui-backend::partials._scripts />
    @stack('scripts')

    <!-- Livewire Scripts -->
    {{-- @livewireScripts --}}

    <!-- Flowbite JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.js"></script>
</body>
</html>
