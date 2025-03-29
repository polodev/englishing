@props([
    'type' => 'website', // 'website' or 'content'
    'currentLang' => app()->getLocale(),
    'dropdownPosition' => 'right', // 'right' or 'left'
    'dropdownDirection' => 'bottom' // 'bottom' or 'top'
])

@php
    $languages = [
        'en' => [
            'name' => 'English',
            'native' => 'English',
            'flag' => '🇺🇸'
        ],
        'bn' => [
            'name' => 'Bengali',
            'native' => 'বাংলা',
            'flag' => '🇧🇩'
        ],
        'hi' => [
            'name' => 'Hindi',
            'native' => 'हिन्दी',
            'flag' => '🇮🇳'
        ],
        'es' => [
            'name' => 'Spanish',
            'native' => 'Español',
            'flag' => '🇪🇸'
        ]
    ];
    
    $title = $type === 'website' ? 'Website Language' : 'Content Language';
    $dropdownClass = $dropdownPosition === 'right' ? 'right-0' : 'left-0';
    $dropdownPositionClass = $dropdownDirection === 'top' ? 'bottom-full mb-2' : 'top-full mt-2';
@endphp

<div x-data="{ open: false }" class="relative">
    <button @click="open = !open" type="button" class="flex items-center text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none">
        <span class="sr-only">{{ $title }}</span>
        @if(isset($languages[$currentLang]))
            <span>{{ $languages[$currentLang]['flag'] }}</span>
            <span class="ml-1">{{ strtoupper($currentLang) }}</span>
        @else
            <span>🇺🇸</span>
            <span class="ml-1">EN</span>
        @endif
        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
    </button>
    <div x-show="open" @click.away="open = false" class="absolute {{ $dropdownClass }} {{ $dropdownPositionClass }} w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-10">
        <div class="py-1">
            @foreach($languages as $code => $lang)
                @if($type === 'website')
                    <a href="{{ LaravelLocalization::getLocalizedURL($code, null, [], true) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $currentLang === $code ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <span class="mr-2">{{ $lang['flag'] }}</span> {{ $lang['native'] }}
                    </a>
                @else
                    <button type="button" 
                            class="w-full text-left flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 {{ $currentLang === $code ? 'bg-gray-100 dark:bg-gray-700' : '' }}"
                            @click="$dispatch('content-language-change', { lang: '{{ $code }}' }); open = false;">
                        <span class="mr-2">{{ $lang['flag'] }}</span> {{ $lang['native'] }}
                    </button>
                @endif
            @endforeach
        </div>
    </div>
</div>
