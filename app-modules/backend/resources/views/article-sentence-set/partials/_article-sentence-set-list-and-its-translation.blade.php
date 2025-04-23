@php
    // Get all sentence set lists for this article sentence set
    $sentenceSetLists = $articleSentenceSet->lists()->with('translations')->orderBy('display_order')->get();
@endphp

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden my-4">
    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Sentence Set Lists</h3>
            <livewire:article-sentence--article-sentence-set-list-edit-using-json :articleSentenceSet="$articleSentenceSet" />
        </div>
    </div>

    @if($sentenceSetLists->isEmpty())
        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
            No sentence lists found for this set. Use the "Edit Using JSON" button to add sentences.
        </div>
    @else
        <div class="overflow-x-auto max-h-[600px]">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-16">ID</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[130px]">Sentence</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[130px]">Slug</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">Order</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($sentenceSetLists as $sentenceList)
                        <!-- Sentence Row -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $sentenceList->id }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                {{ $sentenceList->sentence }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $sentenceList->slug }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $sentenceList->display_order }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                                    @if($sentenceList->pronunciation)
                                        <div><span class="font-medium">Pronunciation:</span> {{ is_array($sentenceList->pronunciation) ? json_encode($sentenceList->pronunciation) : $sentenceList->pronunciation }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Translation Rows -->
                        @foreach($sentenceList->translations as $translation)
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <td class="pl-8 py-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="uppercase">{{ $translation->locale }}</span>
                                </td>
                                <td colspan="4" class="px-4 py-2">
                                    <div class="text-sm space-y-2">
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Translation:</span>
                                            <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">{{ $translation->translation }}</span>
                                            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $translation->transliteration }})</span>
                                        </div>
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
