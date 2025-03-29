
<li {{ $attributes->merge(['class' => 'flex space-x-3']) }} >
  <span class="h-5 w-5 flex justify-center items-center rounded-full bg-blue-600 text-white dark:bg-blue-500">
    <svg class="flex-shrink-0 h-3.5 w-3.5" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
  </span>
  <span class="text-gray-800 dark:text-gray-400">
    {{ $slot }}
  </span>
</li>