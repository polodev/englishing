<div class="mb-6">
    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Word Sets</h2>
    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
        <!-- Article Word Set -->
        <div class="mb-4">
            <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                @if($articleWordSet)
                    <li class="py-2 flex justify-between items-center">
                        <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline" data-id="{{ $articleWordSet->id }}">
                            {{ $articleWordSet->title }}
                        </a>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            @foreach($articleWordSet->getTranslations('title_translation') as $locale => $translation)
                                <span class="inline-block {{ $locale == 'bn' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' }} px-2 py-1 rounded font-bold uppercase {{ !$loop->last ? 'mr-1' : '' }}">{{ strtoupper($locale) }}</span>
                            @endforeach
                        </span>
                    </li>
                @else
                    <li class="py-4 text-center text-gray-500 dark:text-gray-400 italic">
                        No word set found for this article.
                    </li>
                @endif
            </ul>
        </div>
        
        <!-- Add Word Set Component -->
        <div>
            <livewire:article-word--create-article-word-set :articleId="$article->id" />
        </div>
    </div>
</div>
