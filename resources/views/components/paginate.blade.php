@props([
    'data',
])

<nav role="navigation" aria-label="Pagination Navigation" class="flex align-center bg-gray-200 dark:bg-gray-700 px-4 py-4 rounded-md">
  <div class="flex basis-1/3 justify-start">
    @if($data->previousPageUrl())
      <x-a-link href="{{ $data->previousPageUrl() }}" class="font-bold">
        {!!__('pagination.previous')!!} 
      </x-a-link>
    @endif
  </div>

  <div class="flex basis-1/3 justify-center">
    <span class="font-bold">
      {{__('common.page')}}  {{ Number::format($data->currentPage(), locale: app()->getLocale()) }}
    </span>
  </div>

  <div class="flex basis-1/3 justify-end">
    @if($data->nextPageUrl())
      <x-a-link href="{{ $data->nextPageUrl() }}" class="font-bold ">
        {!!__('pagination.next')!!}
      </x-a-link>
    @endif
  </div>
</nav>
