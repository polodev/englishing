<x-ui-backend::layout>

<div class="container mx-auto px-4 py-6">
    <div class="mb-5">
        <a href="{{ route('backend::words.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Back to Words List
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-6 py-4 rounded-t-lg">
            <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">{{ $word->word }}</h1>
            <div class="text-gray-500 dark:text-gray-400 text-sm">
                <span>Created: {{ $word->created_at->format('Y-m-d H:i:s') }} | Updated: {{ $word->updated_at->format('Y-m-d H:i:s') }}</span>
            </div>
        </div>

        <div class="px-6 py-5 dark:bg-gray-800">
            <!-- Phonetic Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Phonetic</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    @if($word->phonetic)
                        <div class="text-lg dark:text-gray-200">{{ $word->phonetic }}</div>
                    @else
                        <div class="text-gray-500 dark:text-gray-400">No phonetic available</div>
                    @endif
                </div>
            </div>

            <!-- Pronunciation Section (non-English locales) -->
            @if($word->getTranslations('pronunciation'))
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Pronunciation</h2>
                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                    @foreach($word->getTranslations('pronunciation') as $locale => $pronunciation)
                        <div class="mb-1"><span class="font-bold text-blue-600 dark:text-blue-400 inline-block w-10">{{ strtoupper($locale) }}:</span> <span class="dark:text-gray-200">{{ $pronunciation }}</span></div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Meanings with Translations Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Meanings with Translations</h2>
                @forelse($word->meanings as $index => $meaning)
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-4">
                        <div class="text-lg font-semibold mb-3 dark:text-gray-200">{{ $index + 1 }}. {{ $meaning->meaning }}</div>
                        
                        @if($meaning->translations->count() > 0)
                            <div class="ml-4 p-3 bg-white dark:bg-gray-600 rounded border-l-4 border-blue-500">
                                <div class="font-medium mb-2 dark:text-gray-200">Translations:</div>
                                @foreach($meaning->translations as $translation)
                                    <div class="mb-1">
                                        <span class="font-bold text-blue-600 dark:text-blue-400 inline-block w-10">{{ strtoupper($translation->locale) }}:</span> 
                                        <span class="dark:text-gray-200">{{ $translation->translation }}</span>
                                        @if($translation->transliteration)
                                            <span class="ml-2 italic text-gray-600 dark:text-gray-400">({{ $translation->transliteration }})</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="ml-4 p-3 bg-white dark:bg-gray-600 rounded border-l-4 border-blue-500">
                                <div class="text-gray-500 dark:text-gray-400">No translations available</div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No meanings available for this word.</div>
                @endforelse
            </div>
            
            <!-- Standalone Translations Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Standalone Translations</h2>
                @if($word->translations->count() > 0)
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        @foreach($word->translations as $translation)
                            <div class="mb-2">
                                <span class="font-bold text-blue-600 dark:text-blue-400 inline-block w-10">{{ strtoupper($translation->locale) }}:</span> 
                                <span class="dark:text-gray-200">{{ $translation->translation }}</span>
                                @if($translation->transliteration)
                                    <span class="ml-2 italic text-gray-600 dark:text-gray-400">({{ $translation->transliteration }})</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No standalone translations available for this word.</div>
                @endif
            </div>

            <!-- Synonyms Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Synonyms</h2>
                @if($synonyms->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($synonyms as $synonym)
                            <a href="{{ route('backend::words.show', $synonym->id) }}" class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white dark:text-gray-200 text-gray-700 rounded-full text-sm transition-colors">
                                {{ $synonym->word }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No synonyms available for this word.</div>
                @endif
            </div>

            <!-- Antonyms Section -->
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Antonyms</h2>
                @if($antonyms->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($antonyms as $antonym)
                            <a href="{{ route('backend::words.show', $antonym->id) }}" class="inline-block px-3 py-1 bg-gray-100 dark:bg-gray-700 hover:bg-blue-500 dark:hover:bg-blue-600 hover:text-white dark:text-gray-200 text-gray-700 rounded-full text-sm transition-colors">
                                {{ $antonym->word }}
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-200 p-4 rounded-md">No antonyms available for this word.</div>
                @endif
            </div>
        </div>
    </div>
</div>
</x-ui-backend::layout>
