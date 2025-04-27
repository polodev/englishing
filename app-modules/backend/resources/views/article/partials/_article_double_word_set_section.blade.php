<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Double Word Sets</h2>
    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
        @if($articleDoubleWordSet)
            <!-- Display existing Article Double Word Set -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="{{ route('backend::article-double-word-sets.show', $articleDoubleWordSet->id) }}" class="text-gray-600 dark:text-gray-400 font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded" title="Double Word Set ID">
                        #{{ $articleDoubleWordSet->id }}
                    </a>
                    <a href="{{ route('backend::article-double-word-sets.show', $articleDoubleWordSet->id) }}" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                        {{ $articleDoubleWordSet->title }}
                    </a>
                </div>
                <div class="flex flex-col space-y-1 text-sm">
                    <!-- Direct access to BN translation -->
                    <div class="flex items-center">
                        <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded font-bold uppercase mr-2">
                            BN
                        </span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $articleDoubleWordSet->getTranslation('title_translation', 'bn', false) }}</span>
                    </div>

                    <!-- Direct access to HI translation -->
                    <div class="flex items-center">
                        <span class="inline-block bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 px-2 py-1 rounded font-bold uppercase mr-2">
                            HI
                        </span>
                        <span class="text-gray-700 dark:text-gray-300">{{ $articleDoubleWordSet->getTranslation('title_translation', 'hi', false) }}</span>
                    </div>
                </div>
            </div>
        @else
            <!-- Show Create Component when no Double Word Set exists -->
            <livewire:article-double-word--create-article-double-word-set :articleId="$article->id" />
        @endif
    </div>
</div>
