<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Word Sets</h2>
    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
        @if($articleWordSet)
            <!-- Display existing Article Word Set -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <a href="#" class="text-gray-600 dark:text-gray-400 font-mono bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded" title="Word Set ID">
                        #{{ $articleWordSet->id }}
                    </a>
                    <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline font-medium">
                        {{ $articleWordSet->title }}
                    </a>
                </div>
                <div class="flex flex-col space-y-1 text-sm">
                    @foreach($articleWordSet->getTranslations('title_translation') as $locale => $translation)
                        <div class="flex items-center">
                            <span class="inline-block {{ $locale == 'bn' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }} px-2 py-1 rounded font-bold uppercase mr-2">
                                {{ strtoupper($locale) }}
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $translation }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <!-- Show Create Component when no Word Set exists -->
            <livewire:article-word--create-article-word-set :articleId="$article->id" />
        @endif
    </div>
</div>
