<div class="flex flex-col items-center">
    <div class="mb-2">
        <img src="{{ asset('img/logos/logo-light.png') }}" alt="Englishing" class="h-16 dark:hidden">
        <img src="{{ asset('img/logos/logo-dark.png') }}" alt="Englishing" class="h-16 hidden dark:block">
    </div>
    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $slot }}</p>
</div>
