@php
    // Get all expression set lists for this article expression set
    $expressionSetLists = $articleExpressionSet->lists()->with('translations')->orderBy('display_order')->get();
@endphp

<div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden my-4">
    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 border-b border-gray-200 dark:border-gray-600">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Expression Set Lists</h3>
            <livewire:article-expression--article-expression-set-list-edit-using-json :articleExpressionSet="$articleExpressionSet" />
        </div>
    </div>

    @if($expressionSetLists->isEmpty())
        <div class="p-4 text-center text-gray-500 dark:text-gray-400">
            No expression lists found for this set. Use the "Edit Using JSON" button to add expressions.
        </div>
    @else
        <div class="overflow-x-auto max-h-[600px]">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-16">ID</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[130px]">Expression</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider min-w-[130px]">Slug</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-24">Order</th>
                        <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Details</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($expressionSetLists as $expressionList)
                        <!-- Expression Row -->
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $expressionList->id }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300 font-medium">
                                {{ $expressionList->expression }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $expressionList->slug }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $expressionList->display_order }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-gray-700 dark:text-gray-300 space-y-2">
                                    @if($expressionList->pronunciation)
                                        <div><span class="font-medium">Pronunciation:</span> {{ is_array($expressionList->pronunciation) ? json_encode($expressionList->pronunciation) : $expressionList->pronunciation }}</div>
                                    @endif
                                    
                                    @if($expressionList->meaning)
                                        <div><span class="font-medium">Meaning:</span> {{ $expressionList->meaning }}</div>
                                    @endif
                                    
                                    @if($expressionList->example_sentence)
                                        <div><span class="font-medium">Example Sentence:</span> {{ $expressionList->example_sentence }}</div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Translation Rows -->
                        @foreach($expressionList->translations as $translation)
                            <tr class="bg-gray-50 dark:bg-gray-900">
                                <td class="pl-8 py-2 text-xs text-gray-500 dark:text-gray-400">
                                    <span class="uppercase">{{ $translation->locale }}</span>
                                </td>
                                <td colspan="4" class="px-4 py-2">
                                    <div class="text-sm space-y-2">
                                        <div>
                                            <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">Translation:</span>
                                            <span class="ml-2 text-sm text-gray-800 dark:text-gray-200">{{ $translation->expression_translation }}</span>
                                            @if($translation->expression_transliteration)
                                                <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $translation->expression_transliteration }})</span>
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
