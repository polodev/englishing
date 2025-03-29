<a {{ $attributes->merge(['href' => '#', 'class' => 'py-1 px-2 inline-flex items-center gap-x-1 text-sm font-medium bg-orange-100 hover:bg-orange-300 text-orange-800 rounded-full dark:bg-orange-500/10 dark:hover:bg-orange-500/30 dark:text-orange-500']) }}> 
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 8.25h15m-16.5 7.5h15m-1.8-13.5-3.9 19.5m-2.1-19.5-3.9 19.5" />
  </svg>
  {{ $slot }}
</a>
