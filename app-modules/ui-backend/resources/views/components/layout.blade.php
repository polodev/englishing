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
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@100..900&display=swap" rel="stylesheet">


    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="{{ asset('vendor/jquery/jquery.js') }}"></script>

    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.2.0/flowbite.min.css" rel="stylesheet" />
    <script src="{{ asset('vendor/flatpickr/flatpickr.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('vendor/datatables/css/jquery.dataTables.min.css') }}">

    <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.3/css/fixedHeader.dataTables.min.css">

    <!-- EasyMDE Markdown Editor -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde@2.15.0/dist/easymde.min.css">
    <script src="https://cdn.jsdelivr.net/npm/easymde@2.15.0/dist/easymde.min.js"></script>
    <style>
        /* Fix EasyMDE fullscreen z-index */
        .EasyMDEContainer .CodeMirror-fullscreen,
        .EasyMDEContainer .editor-toolbar.fullscreen,
        .EasyMDEContainer .editor-preview-side,
        .EasyMDEContainer.editor-preview-active-side .CodeMirror,
        .editor-toolbar.fullscreen,
        .CodeMirror-fullscreen,
        .editor-preview-side {
            z-index: 9999 !important;
        }
    </style>

    <!-- Marked.js for Markdown Preview -->
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .sidebar-transition {
            transition: width 0.3s ease;
        }
    </style>

    <!-- Livewire Styles -->
    @livewireStyles

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @stack('styles')
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
