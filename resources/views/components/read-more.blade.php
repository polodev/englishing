@props(['readMoreUrl', 'isBlank'])
 <div {{ $attributes->merge(['class' => 'flex justify-end mt-4']) }}>
  <x-button-primary-link
    target="{{ isset($isBlank) && $isBlank ? '_blank' : '_self' }}"
    href="{{ $readMoreUrl }}">
    {{ __('common.read_more') }}
  </x-button-primary-link>
</div>