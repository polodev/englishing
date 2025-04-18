@php
    // Get all word set lists for this article word set
    $wordSetLists = $articleWordSet->lists()->with('translations')->orderBy('display_order')->get();
@endphp

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden my-4">
    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Word Set Lists</h3>
            <livewire:article-word--article-word-set-list-edit-using-json :articleWordSet="$articleWordSet" />
        </div>
    </div>

    @if($wordSetLists->isEmpty())
        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
            No word lists found for this set. Use the "Edit JSON" button to add words.
        </div>
    @else
        <div class="overflow-x-auto max-h-[600px]">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-16">ID</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[130px]">Word</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[130px]">Slug</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[120px]">Phonetic</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">Order</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($wordSetLists as $wordList)
                        <!-- Word Row -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $wordList->id }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                {{ $wordList->word }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $wordList->slug }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $wordList->phonetic ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $wordList->display_order }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                                    @if($wordList->parts_of_speech)
                                        <div><span class="font-medium">Parts of Speech:</span> {{ $wordList->parts_of_speech }}</div>
                                    @endif
                                    
                                    @if($wordList->pronunciation)
                                        <div><span class="font-medium">Pronunciation:</span> {{ is_array($wordList->pronunciation) ? json_encode($wordList->pronunciation) : $wordList->pronunciation }}</div>
                                    @endif
                                    
                                    @if($wordList->static_content_1)
                                        <div><span class="font-medium">Static Content 1:</span> {{ $wordList->static_content_1 }}</div>
                                    @endif
                                    
                                    @if($wordList->static_content_2)
                                        <div><span class="font-medium">Static Content 2:</span> {{ $wordList->static_content_2 }}</div>
                                    @endif
                                    
                                    @if($wordList->meaning)
                                        <div><span class="font-medium">Meaning:</span> {{ $wordList->meaning }}</div>
                                    @endif
                                    
                                    @if($wordList->example_sentence)
                                        <div><span class="font-medium">Example Sentence:</span> {{ $wordList->example_sentence }}</div>
                                    @endif
                                    
                                    @if($wordList->example_expression)
                                        <div><span class="font-medium">Example Expression:</span> {{ $wordList->example_expression }}</div>
                                    @endif
                                    
                                    @if($wordList->example_expression_meaning)
                                        <div><span class="font-medium">Example Expression Meaning:</span> {{ $wordList->example_expression_meaning }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Translation Rows -->
                        @foreach($wordList->translations as $translation)
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <td class="pl-8 py-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="uppercase">{{ $translation->locale }}</span>
                                </td>
                                <td colspan="5" class="px-4 py-2">
                                    <div class="text-sm space-y-2">
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Translation:</span>
                                            <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">{{ $translation->word_translation }}</span>
                                            @if($translation->word_transliteration)
                                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $translation->word_transliteration }})</span>
                                            @endif
                                        </div>
                                        
                                        @if($translation->example_sentence_translation)
                                            <div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Example Sentence:</span>
                                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">{{ $translation->example_sentence_translation }}</span>
                                                @if($translation->example_sentence_transliteration)
                                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $translation->example_sentence_transliteration }})</span>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        @if($translation->example_expression_translation)
                                            <div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Example Expression:</span>
                                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">{{ $translation->example_expression_translation }}</span>
                                                @if($translation->example_expression_transliteration)
                                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $translation->example_expression_transliteration }})</span>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        @if($translation->source)
                                            <div>
                                                <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Source:</span>
                                                <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">{{ $translation->source }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
