<div {{ $attributes->merge(['class' => 'bg-red-100 border border-red-200 text-sm text-red-800 rounded-lg p-4 dark:bg-red-800/10 dark:border-red-900 dark:text-red-500']) }} role="alert">
  {{$slot}}
</div>