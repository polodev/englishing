<x-ui-backend::layout>
    <div class="container mx-auto px-4 py-6">
        <div class="mb-5 flex justify-between">
            <a href="{{ route('backend::articles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Articles List
            </a>
            <a href="{{ route('backend::articles.edit', $article) }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit Article
            </a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600 px-6 py-4 rounded-t-lg">
                <h1 class="text-4xl font-bold text-gray-800 dark:text-white mb-2">{{ $article->title }}</h1>
                <div class="text-gray-500 dark:text-gray-400 text-sm">
                    <span>Created: {{ $article->created_at->format('Y-m-d H:i:s') }} | Updated: {{ $article->updated_at->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>

            <div class="px-6 py-5 dark:bg-gray-800">
                <!-- Article Info Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Article Information</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <div class="mb-2">
                                    <span class="font-bold text-gray-600 dark:text-gray-400">Title:</span>
                                    <span class="dark:text-gray-200">{{ $article->title }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="font-bold text-gray-600 dark:text-gray-400">Slug:</span>
                                    <span class="dark:text-gray-200">{{ $article->slug }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="font-bold text-gray-600 dark:text-gray-400">Author:</span>
                                    <span class="dark:text-gray-200">{{ $article->user ? $article->user->name : 'N/A' }}</span>
                                </div>
                                <div class="mb-2">
                                    <span class="font-bold text-gray-600 dark:text-gray-400">Premium Content:</span>
                                    <span class="dark:text-gray-200">{{ $article->is_premium ? 'Yes' : 'No' }}</span>
                                </div>
                            </div>
                            <div>
                                <div class="mb-2">
                                    <span class="font-bold text-gray-600 dark:text-gray-400">Course:</span>
                                    <span class="dark:text-gray-200">
                                        @if($article->course)
                                            <a href="{{ route('backend::courses.show', $article->course) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                {{ $article->course->title }}
                                            </a>
                                        @else
                                            No Course
                                        @endif
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <span class="font-bold text-gray-600 dark:text-gray-400">Display Order:</span>
                                    <span class="dark:text-gray-200">{{ $article->display_order }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Article Content Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Content</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        @if($article->excerpt)
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Excerpt</h3>
                                <div class="prose dark:prose-invert max-w-none dark:text-gray-200 bg-gray-50 dark:bg-gray-700 p-3 rounded-md font-mono text-sm whitespace-pre-wrap">
                                    {{ $article->excerpt }}
                                </div>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-lg font-semibold text-gray-700 dark:text-gray-300 mb-2">Main Content</h3>
                            <div class="prose dark:prose-invert max-w-none dark:text-gray-200 bg-gray-50 dark:bg-gray-700 p-3 rounded-md font-mono text-sm whitespace-pre-wrap">
                                {{ $article->content }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Title Translations Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Title Translations</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($article->getTranslations('title_translation') as $locale => $translation)
                                <div>
                                    <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded text-xs font-bold uppercase mb-1">{{ strtoupper($locale) }}</span>
                                    <div class="dark:text-gray-200">{{ $translation }}</div>
                                </div>
                            @endforeach

                            @if(count($article->getTranslations('title_translation')) === 0)
                                <div class="col-span-2 text-gray-500 dark:text-gray-400 italic">No translations available</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Content Translations Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Content Translations</h2>
                    
                    @if(count($article->getTranslations('content_translation')) === 0)
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <div class="text-gray-500 dark:text-gray-400 italic">No translations available</div>
                        </div>
                    @else
                        @foreach($article->getTranslations('content_translation') as $locale => $translation)
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md mb-4">
                                <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded text-xs font-bold uppercase mb-2">{{ strtoupper($locale) }}</span>
                                <div class="prose dark:prose-invert max-w-none dark:text-gray-200 bg-gray-50 dark:bg-gray-700 p-3 rounded-md font-mono text-sm whitespace-pre-wrap">
                                    {{ $translation }}
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                <!-- Excerpt Translations Section -->
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Excerpt Translations</h2>
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($article->getTranslations('excerpt_translation') as $locale => $translation)
                                <div>
                                    <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 px-2 py-1 rounded text-xs font-bold uppercase mb-1">{{ strtoupper($locale) }}</span>
                                    <div class="dark:text-gray-200">{{ $translation }}</div>
                                </div>
                            @endforeach

                            @if(count($article->getTranslations('excerpt_translation')) === 0)
                                <div class="col-span-2 text-gray-500 dark:text-gray-400 italic">No translations available</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Article Word Sets Section -->
                @include('backend::article.partials._article_word_set_section')

                <!-- Article Expression Sets Section -->
                @include('backend::article.partials._article_expression_set_section')

                <!-- Associated Articles Section -->
                @if(count($associatedArticles) > 0)
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-700 dark:text-gray-300 border-b-2 border-gray-200 dark:border-gray-600 pb-2 mb-3">Other Articles in This Course</h2>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-md">
                            <ul class="divide-y divide-gray-200 dark:divide-gray-600">
                                @foreach($associatedArticles as $assocArticle)
                                    <li class="py-2">
                                        <a href="{{ route('backend::articles.show', $assocArticle['id']) }}" class="text-blue-600 dark:text-blue-400 hover:underline">
                                            {{ $assocArticle['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-ui-backend::layout>
